const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const postcss = require( 'postcss' );
const prefixSelector = require( 'postcss-prefix-selector' );

const PREFIX = '.draad-adreszoeker';

/**
 * Webpack plugin that scopes CSS strings injected at runtime by @gemeente-denhaag
 * and @utrecht component packages. These packages embed their CSS as string
 * literals and inject them via <style> tags when their JS modules load.
 * Without scoping, those styles leak globally and can break other page elements.
 *
 * This plugin intercepts emitted JS bundles, finds the embedded CSS strings,
 * and prefixes all selectors with .draad-adreszoeker before the files are written.
 */
class ScopeInjectedCssPlugin {
	apply( compiler ) {
		compiler.hooks.thisCompilation.tap(
			'ScopeInjectedCssPlugin',
			( compilation ) => {
				compilation.hooks.processAssets.tapPromise(
					{
						name: 'ScopeInjectedCssPlugin',
						stage: compiler.webpack.Compilation
							.PROCESS_ASSETS_STAGE_SUMMARIZE,
					},
					async ( assets ) => {
						const processor = postcss( [
							prefixSelector( {
								prefix: PREFIX,
								transform( prefix, selector, prefixed ) {
									if (
										selector === prefix ||
										selector.startsWith( prefix + ' ' ) ||
										selector.startsWith( prefix + '.' ) ||
										selector.startsWith( prefix + ':' )
									) {
										return selector;
									}
									if (
										selector === ':root' ||
										selector === 'html' ||
										selector === 'body'
									) {
										return prefix;
									}
									return prefixed;
								},
							} ),
						] );

						for ( const [ filename, asset ] of Object.entries(
							assets
						) ) {
							if ( ! filename.endsWith( '.js' ) ) continue;

							let source = asset.source();
							const replacements = [];

							// Find quoted CSS strings from design system packages.
							// These are string literals that start with a .denhaag- or
							// .utrecht- selector and contain CSS rules (have { and }).
							const pattern =
								/"(\.(?:denhaag|utrecht)-[a-zA-Z][^"]{50,})"/g;
							let match;

							while ( ( match = pattern.exec( source ) ) !== null ) {
								const raw = match[ 1 ];

								// Validate it looks like CSS
								if (
									! raw.includes( '{' ) ||
									! raw.includes( '}' )
								) {
									continue;
								}

								// Skip if already scoped
								if ( raw.includes( PREFIX ) ) continue;

								// Unescape JS string escapes to get real CSS
								const css = raw
									.replace( /\\n/g, '\n' )
									.replace( /\\t/g, '\t' )
									.replace( /\\\\/g, '\\' );

								try {
									const result = await processor.process(
										css,
										{ from: undefined }
									);
									const prefixedCss = result.css;

									// Re-escape for JS string literal
									const escaped = prefixedCss
										.replace( /\\/g, '\\\\' )
										.replace( /\n/g, '\\n' )
										.replace( /\t/g, '\\t' );

									if ( escaped !== raw ) {
										replacements.push( {
											original: `"${ raw }"`,
											replacement: `"${ escaped }"`,
										} );
									}
								} catch ( e ) {
									// Skip strings PostCSS cannot parse
								}
							}

							if ( replacements.length > 0 ) {
								let newSource = source;
								for ( const {
									original,
									replacement,
								} of replacements ) {
									newSource = newSource
										.split( original )
										.join( replacement );
								}

								assets[ filename ] = new compiler.webpack.sources.RawSource(
									newSource
								);
							}
						}
					}
				);
			}
		);
	}
}

module.exports = {
	...defaultConfig,
	plugins: [ ...defaultConfig.plugins, new ScopeInjectedCssPlugin() ],
};
