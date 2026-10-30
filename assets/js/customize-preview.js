/**
 * 自定义器实时预览：改颜色、模糊、圆角时不用刷新就能看到效果。
 */
(function (api) {
	'use strict';

	var root = document.documentElement;

	var bindVar = function (setting, variable, suffix) {
		api(setting, function (value) {
			value.bind(function (next) {
				root.style.setProperty(variable, next + (suffix || ''));
			});
		});
	};

	bindVar('pureaura_accent', '--accent', '');
	bindVar('pureaura_blur', '--blur', 'px');
	bindVar('pureaura_radius', '--r', 'px');
	bindVar('pureaura_maxw', '--maxw', 'px');

	api('pureaura_glass', function (value) {
		value.bind(function (next) {
			root.style.setProperty('--glass-a', (Number(next) / 100).toFixed(2));
		});
	});

	api('pureaura_theme_mode', function (value) {
		value.bind(function (next) {
			var target = next;
			if (next === 'auto') {
				target = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
			}
			root.setAttribute('data-theme', target);
			root.setAttribute('data-theme-mode', next);
		});
	});

	api('pureaura_layout', function (value) {
		value.bind(function (next) {
			document.body.className = document.body.className.replace(/layout-\w+/g, 'layout-' + next);
		});
	});

	api('pureaura_rail_side', function (value) {
		value.bind(function (next) {
			document.body.className = document.body.className.replace(/rail-\w+/g, 'rail-' + next);
		});
	});
})(wp.customize);
