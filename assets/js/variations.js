const UCSC_ALIGNED_IMAGE = 'ucsc-custom-functionality/aligned-image';
const { registerBlockVariation } = wp.blocks;

/**
 * Add core/group block variation for aligned images 
 */
registerBlockVariation( 'core/group', {
    name: UCSC_ALIGNED_IMAGE,
    title: 'Aligned Image',
    description: 'Insert an aligned image so text can flow around it.',
    keywords: [ "img", "photo", "picture", "image", "align", "left", "right" ],
    attributes: {
		layout: {
			type: 'constrained'
		},
        allowedBlocks: [ "core/image", "core/spacer" ],
        className: 'ucsc-aligned-image__container',
        style: {
            spacing: {
                margin: {
                    bottom: '1'
                }
            }    
        }
	},
    isActive: ( { namespace, query } ) => {
        return (
            namespace === UCSC_ALIGNED_IMAGE
            && query.postType === 'post'
        );
    },
    icon: 'image-flip-horizontal',
    scope: [ 'inserter' ],
    innerBlocks: [
        ['core/image',{"className":"ucsc-aligned-image__item","sizeSlug":"large","linkDestination":"none","align":"center","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}}]
    ]
});