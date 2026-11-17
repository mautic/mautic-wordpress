( function ( blocks, element, blockEditor, editor, components, serverSideRender, i18n ) {
	var el = element.createElement;
	var __ = i18n.__;
	var editorPackage = blockEditor || editor || {};
	var InspectorControls = editorPackage.InspectorControls;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
	var ServerSideRender = ( serverSideRender && ( serverSideRender.default || serverSideRender ) )
		|| editorPackage.ServerSideRender
		|| ( components && components.ServerSideRender );

	function getBlockProps( extra ) {
		if ( editorPackage.useBlockProps ) {
			return editorPackage.useBlockProps( extra );
		}

		return extra;
	}

	function mauticIcon() {
		return el(
			'svg',
			{
				xmlns: 'http://www.w3.org/2000/svg',
				viewBox: '-150.2 325.8 122.8 122.8',
				width: 24,
				height: 24
			},
			el( 'path', {
				fill: '#4E5E9E',
				d: 'M-88.8,446.6c-32.7,0-59.4-26.6-59.4-59.4s26.7-59.4,59.4-59.4c7.9,0,15.6,1.5,22.8,4.6c2,0.9,3,3.2,2.2,5.3c-0.9,2-3.2,3-5.3,2.2c-6.3-2.6-12.9-3.9-19.7-3.9c-28.3,0-51.3,23-51.3,51.3s23,51.3,51.3,51.3s51.3-23,51.3-51.3c0-6.1-1-12-3.1-17.6c-0.8-2.1,0.3-4.4,2.4-5.2c2.1-0.8,4.4,0.3,5.2,2.4c2.4,6.5,3.6,13.4,3.6,20.4C-29.4,419.9-56,446.6-88.8,446.6z'
			} ),
			el( 'polygon', {
				fill: '#FDB933',
				points: '-62.8,381.1 -72,390.7 -66.9,412.2 -55.3,412.2'
			} ),
			el( 'polygon', {
				fill: '#FDB933',
				points: '-66.6,352.7 -63.4,355.9 -88.8,382.8 -110.5,360.5 -123,412.2 -111.4,412.2 -104.5,383.5 -88.8,400.6 -55.2,364 -52,367.3 -48.8,349.2'
			} )
		);
	}

	function formIdControl( formId, setAttributes ) {
		return el( TextControl, {
			key: 'wpmautic-form-id',
			label: __( 'Form ID', 'wp-mautic' ),
			type: 'number',
			min: 1,
			value: formId,
			onChange: function ( value ) {
				setAttributes( { formId: value ? String( value ) : '' } );
			}
		} );
	}

	blocks.registerBlockType( 'wp-mautic/form', {
		title: __( 'Mautic Form', 'wp-mautic' ),
		category: 'embed',
		description: __( 'Insert a Mautic form and preview it in the editor.', 'wp-mautic' ),
		keywords: [ 'mautic', 'form' ],
		supports: {
			html: false,
			anchor: false
		},
		attributes: {
			formId: {
				type: 'string',
				default: ''
			}
		},
		icon: mauticIcon,
		edit: function ( props ) {
			var formId = props.attributes.formId;
			var blockProps = getBlockProps( { className: 'wpmautic-form-block' } );
			var inspector = InspectorControls
				? el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Mautic Form', 'wp-mautic' ), initialOpen: true },
						formIdControl( formId, props.setAttributes )
					)
				)
				: null;
			var setup = el(
				'div',
				{ className: 'wpmautic-form-block__setup' },
				el( 'p', { className: 'wpmautic-form-block__title' }, __( 'Mautic Form', 'wp-mautic' ) ),
				el( 'p', { className: 'wpmautic-form-block__help' }, __( 'Enter a form ID to insert the form and preview it here.', 'wp-mautic' ) ),
				formIdControl( formId, props.setAttributes )
			);
			var preview = formId && ServerSideRender
				? el( ServerSideRender, {
					block: 'wp-mautic/form',
					attributes: props.attributes
				} )
				: null;

			return el( 'div', blockProps, inspector, setup, preview );
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.editor, window.wp.components, window.wp.serverSideRender, window.wp.i18n );
