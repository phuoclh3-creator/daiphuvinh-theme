/**
 * Khối trang chủ Đại Phú Vinh (không cần build): hiển thị bản xem trước do PHP render
 * và cho chỉnh thông số ở thanh bên phải.
 */
( function ( wp ) {
	'use strict';
	var el = wp.element.createElement;
	var registerBlockType = wp.blocks.registerBlockType;
	var ServerSideRender = wp.serverSideRender;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var SelectControl = wp.components.SelectControl;
	var RangeControl = wp.components.RangeControl;
	var useSelect = wp.data.useSelect;

	/** Chọn danh mục từ REST; nếu không tải được thì cho nhập slug. */
	function TermSelect( props ) {
		var terms = useSelect( function ( select ) {
			return select( 'core' ).getEntityRecords( 'taxonomy', props.taxonomy, { per_page: 100, hide_empty: false } );
		}, [ props.taxonomy ] );
		if ( ! terms ) {
			return el( TextControl, { label: props.label, value: props.value, onChange: props.onChange, help: 'Đang tải danh mục…' } );
		}
		var options = [ { label: '— Chọn —', value: '' } ].concat( terms.map( function ( t ) {
			return { label: t.name.replace( /&amp;/g, '&' ), value: t.slug };
		} ) );
		if ( props.value && ! terms.some( function ( t ) { return t.slug === props.value; } ) ) {
			options.push( { label: props.value + ' (chưa tồn tại)', value: props.value } );
		}
		return el( SelectControl, { label: props.label, value: props.value, options: options, onChange: props.onChange } );
	}

	function makeBlock( name, icon, opts ) {
		registerBlockType( name, {
			apiVersion: 3,
			title: opts.title,
			description: opts.description,
			icon: icon,
			category: 'dpv',
			attributes: opts.attributes,
			supports: { html: false, className: false, customClassName: false, reusable: true },
			edit: function ( props ) {
				var a = props.attributes;
				var set = props.setAttributes;
				return el( 'div', useBlockProps(),
					el( InspectorControls, null, opts.controls( a, set ) ),
					el( ServerSideRender, { block: name, attributes: a } )
				);
			},
			save: function () { return null; }
		} );
	}

	function titleControl( a, set, label ) {
		return el( TextControl, { label: label || 'Tiêu đề khối', value: a.title, onChange: function ( v ) { set( { title: v } ); } } );
	}
	function countControl( a, set, max ) {
		return el( RangeControl, { label: 'Số mục hiển thị', value: a.count, min: 1, max: max, onChange: function ( v ) { set( { count: v } ); } } );
	}

	makeBlock( 'dpv/hero', 'align-wide', {
		title: 'Đại Phú Vinh: Danh mục + Banner + Báo giá',
		description: 'Cột danh mục sản phẩm, banner trượt và danh sách Bảng báo giá.',
		attributes: { quoteCategory: { type: 'string', default: 'bang-bao-gia' }, quoteCount: { type: 'number', default: 3 } },
		controls: function ( a, set ) {
			return el( PanelBody, { title: 'Cột Báo giá bên phải', initialOpen: true },
				el( TermSelect, { taxonomy: 'category', label: 'Chuyên mục báo giá', value: a.quoteCategory, onChange: function ( v ) { set( { quoteCategory: v } ); } } ),
				el( RangeControl, { label: 'Số bài hiển thị', value: a.quoteCount, min: 1, max: 8, onChange: function ( v ) { set( { quoteCount: v } ); } } ),
				el( 'p', { style: { color: '#6b7280' } }, 'Ảnh banner thay ở: Giao diện › Tùy biến › Đại Phú Vinh – Trang chủ. Danh mục bên trái lấy từ Menu "Danh mục sản phẩm" (hoặc tự động từ danh mục WooCommerce).' )
			);
		}
	} );

	makeBlock( 'dpv/projects', 'format-gallery', {
		title: 'Đại Phú Vinh: Dự án (3 ảnh lớn)',
		description: 'Lưới ảnh lớn lấy từ một chuyên mục bài viết.',
		attributes: { title: { type: 'string', default: 'Các dự án hoàn thiện' }, category: { type: 'string', default: 'du-an' }, count: { type: 'number', default: 3 } },
		controls: function ( a, set ) {
			return el( PanelBody, { title: 'Cài đặt khối', initialOpen: true },
				titleControl( a, set ),
				el( TermSelect, { taxonomy: 'category', label: 'Chuyên mục bài viết', value: a.category, onChange: function ( v ) { set( { category: v } ); } } ),
				countControl( a, set, 12 )
			);
		}
	} );

	makeBlock( 'dpv/products', 'cart', {
		title: 'Đại Phú Vinh: Sản phẩm theo danh mục',
		description: 'Lưới sản phẩm WooCommerce của một danh mục, kèm nút Xem thêm.',
		attributes: { title: { type: 'string', default: '' }, category: { type: 'string', default: '' }, count: { type: 'number', default: 8 } },
		controls: function ( a, set ) {
			return el( PanelBody, { title: 'Cài đặt khối', initialOpen: true },
				el( TermSelect, { taxonomy: 'product_cat', label: 'Danh mục sản phẩm', value: a.category, onChange: function ( v ) { set( { category: v } ); } } ),
				titleControl( a, set, 'Tiêu đề (để trống = tên danh mục)' ),
				countControl( a, set, 24 )
			);
		}
	} );

	makeBlock( 'dpv/posts', 'media-text', {
		title: 'Đại Phú Vinh: Bài viết theo chuyên mục',
		description: 'Lưới bài viết (cẩm nang, tin tức…) của một chuyên mục.',
		attributes: { title: { type: 'string', default: 'Cẩm nang xây nhà' }, category: { type: 'string', default: 'cam-nang' }, count: { type: 'number', default: 4 } },
		controls: function ( a, set ) {
			return el( PanelBody, { title: 'Cài đặt khối', initialOpen: true },
				titleControl( a, set ),
				el( TermSelect, { taxonomy: 'category', label: 'Chuyên mục bài viết', value: a.category, onChange: function ( v ) { set( { category: v } ); } } ),
				countControl( a, set, 12 )
			);
		}
	} );
} )( window.wp );
