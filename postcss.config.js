const autoprefixer = require( 'autoprefixer' );
const prefixSelector = require( 'postcss-prefix-selector' );

const PREFIX = '.draad-adreszoeker';

const plugins = [
	prefixSelector( {
		prefix: PREFIX,
		transform( prefix, selector, prefixedSelector ) {
			// Don't double-prefix selectors already scoped to .draad-adreszoeker
			// Only skip if the selector IS the prefix or STARTS WITH it as an ancestor/chain
			// (not BEM elements like .draad-adreszoeker__form or block names like .draad-adreszoeker-formulier)
			if (
				selector === prefix ||
				selector.startsWith( prefix + ' ' ) ||
				selector.startsWith( prefix + '.' ) ||
				selector.startsWith( prefix + ':' )
			) {
				return selector;
			}
			// Convert :root, html, body to the prefix class instead of prefixing
			if (
				selector === ':root' ||
				selector === 'html' ||
				selector === 'body'
			) {
				return prefix;
			}
			return prefixedSelector;
		},
	} ),
	autoprefixer( { grid: true } ),
];

if ( process.env.NODE_ENV === 'production' ) {
	plugins.push(
		require( 'cssnano' )( {
			preset: [
				'default',
				{
					discardComments: {
						removeAll: true,
					},
				},
			],
		} )
	);
}

module.exports = { plugins };
