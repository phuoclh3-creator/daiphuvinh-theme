/**
 * Đại Phú Vinh – JS tối giản (không phụ thuộc thư viện).
 * - Banner tự chạy + chấm điều hướng (tôn trọng prefers-reduced-motion)
 * - Menu mobile, dropdown "Danh mục sản phẩm"
 */
( function () {
	'use strict';

	/* ---------- Slider ---------- */
	document.querySelectorAll( '[data-slider]' ).forEach( function ( root ) {
		var slides = root.querySelectorAll( '.slide' );
		var dotsBox = root.querySelector( '.slider__dots' );
		if ( slides.length < 2 ) {
			if ( dotsBox ) { dotsBox.hidden = true; }
			return;
		}
		var idx = 0;
		var timer = null;
		var interval = parseInt( root.getAttribute( 'data-interval' ), 10 ) || 5500;
		var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		var dots = [];

		slides.forEach( function ( s, i ) {
			var b = document.createElement( 'button' );
			b.type = 'button';
			b.className = 'slider__dot' + ( i === 0 ? ' is-active' : '' );
			b.setAttribute( 'aria-label', 'Banner ' + ( i + 1 ) );
			b.addEventListener( 'click', function () { go( i ); restart(); } );
			dotsBox.appendChild( b );
			dots.push( b );
		} );

		function go( n ) {
			slides[ idx ].classList.remove( 'is-active' );
			dots[ idx ].classList.remove( 'is-active' );
			idx = ( n + slides.length ) % slides.length;
			slides[ idx ].classList.add( 'is-active' );
			dots[ idx ].classList.add( 'is-active' );
		}
		function start() { if ( ! reduce ) { timer = window.setInterval( function () { go( idx + 1 ); }, interval ); } }
		function stop() { if ( timer ) { window.clearInterval( timer ); timer = null; } }
		function restart() { stop(); start(); }

		root.addEventListener( 'mouseenter', stop );
		root.addEventListener( 'mouseleave', start );
		root.addEventListener( 'focusin', stop );
		root.addEventListener( 'focusout', start );
		document.addEventListener( 'visibilitychange', function () { document.hidden ? stop() : start(); } );
		start();
	} );

	/* ---------- Menu mobile ---------- */
	var toggle = document.querySelector( '.nav-toggle' );
	var nav = document.getElementById( 'primary-nav' );
	if ( toggle && nav ) {
		toggle.addEventListener( 'click', function () {
			var open = nav.classList.toggle( 'is-open' );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );
	}

	/* ---------- Dropdown danh mục ----------
	 * Trang trong: bấm nút để xổ danh mục.
	 * Trang chủ: danh mục luôn hiện cạnh banner; khi cuộn khỏi khối đó, nút trên thanh menu
	 * (sticky) xổ lại đúng danh mục ấy. */
	var catBtn = document.querySelector( '.catbtn' );
	var catPanel = document.getElementById( 'cat-panel' );
	var heroCats = document.getElementById( 'cat-panel-home' );
	if ( catBtn && catPanel ) {
		var heroVisible = !! heroCats;
		function closeCat() {
			catPanel.hidden = true;
			catBtn.setAttribute( 'aria-expanded', 'false' );
		}
		if ( heroCats ) {
			catBtn.classList.add( 'is-home-btn' );
			if ( 'IntersectionObserver' in window ) {
				// Tính "còn thấy" khi khối danh mục còn nằm dưới thanh menu dính.
				var navH = ( document.querySelector( '.navbar' ) || { offsetHeight: 44 } ).offsetHeight;
				new IntersectionObserver( function ( entries ) {
					heroVisible = entries[ 0 ].isIntersecting;
					catBtn.classList.toggle( 'can-drop', ! heroVisible );
					if ( heroVisible ) { closeCat(); }
				}, { rootMargin: '-' + navH + 'px 0px 0px 0px', threshold: 0 } ).observe( heroCats );
			}
		} else {
			catBtn.classList.add( 'can-drop' );
		}
		catBtn.addEventListener( 'click', function () {
			if ( heroCats && heroVisible ) { return; }
			if ( heroCats && ! catPanel.firstElementChild ) {
				catPanel.innerHTML = heroCats.innerHTML;
			}
			var show = catPanel.hidden;
			catPanel.hidden = ! show;
			catBtn.setAttribute( 'aria-expanded', show ? 'true' : 'false' );
		} );
		document.addEventListener( 'click', function ( e ) {
			if ( ! catPanel.hidden && ! catPanel.contains( e.target ) && ! catBtn.contains( e.target ) ) {
				closeCat();
			}
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && ! catPanel.hidden ) {
				closeCat();
				catBtn.focus();
			}
		} );
	}
} )();
