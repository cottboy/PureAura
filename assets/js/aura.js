/**
 * PureAura 前台交互
 * 全部原生实现，不依赖任何库。所有能力都做了缺失保护：
 * 浏览器不支持时，页面仍然可以正常阅读。
 */
(function () {
	'use strict';

	var root = document.documentElement;
	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	var darkQuery = window.matchMedia('(prefers-color-scheme: dark)');
	var THEME_KEY = 'pureaura-theme';

	/* ---------------- 外观模式 ---------------- */

	function resolveMode(mode) {
		if (mode === 'auto') {
			return darkQuery.matches ? 'dark' : 'light';
		}
		return mode === 'dark' ? 'dark' : 'light';
	}

	function syncControls(mode) {
		var index = { light: 0, auto: 1, dark: 2 }[mode] || 1;

		document.querySelectorAll('[data-seg]').forEach(function (seg) {
			seg.style.setProperty('--seg-index', index);
			seg.querySelectorAll('[data-theme-set]').forEach(function (btn) {
				var on = btn.getAttribute('data-theme-set') === mode;
				btn.setAttribute('aria-pressed', on ? 'true' : 'false');
			});
		});

		var dark = resolveMode(mode) === 'dark';
		document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
			var icon = btn.querySelector('[data-theme-icon]');
			var label = btn.querySelector('[data-theme-label]');
			if (icon) {
				icon.innerHTML = dark
					? '<svg class="icon" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6 7 7M17 17l1.4 1.4M18.4 5.6 17 7M7 17l-1.4 1.4"/></svg>'
					: '<svg class="icon" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13.4A8.2 8.2 0 0 1 10.6 4a8.2 8.2 0 1 0 9.4 9.4Z"/></svg>';
			}
			if (label) {
				label.textContent = dark ? '浅色' : '深色';
			}
		});
	}

	function setMode(mode, event) {
		var next = resolveMode(mode);
		var commit = function () {
			root.setAttribute('data-theme', next);
			root.setAttribute('data-theme-mode', mode);
		};

		try {
			window.localStorage.setItem(THEME_KEY, mode);
		} catch (e) {
			/* 隐私模式下写入失败也不影响本次切换 */
		}

		if (document.startViewTransition && !reduceMotion.matches && event && event.clientX) {
			root.style.setProperty('--vt-x', event.clientX + 'px');
			root.style.setProperty('--vt-y', event.clientY + 'px');
			document.startViewTransition(commit);
		} else {
			commit();
		}

		syncControls(mode);
	}

	document.addEventListener('click', function (event) {
		var btn = event.target.closest('[data-theme-set]');
		if (btn) {
			setMode(btn.getAttribute('data-theme-set'), event);
			return;
		}

		var toggle = event.target.closest('[data-theme-toggle]');
		if (toggle) {
			var current = root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
			setMode(current === 'dark' ? 'light' : 'dark', event);
		}
	});

	darkQuery.addEventListener('change', function () {
		if (root.getAttribute('data-theme-mode') === 'auto') {
			root.setAttribute('data-theme', resolveMode('auto'));
			syncControls('auto');
		}
	});

	syncControls(root.getAttribute('data-theme-mode') || 'auto');

	/* ---------------- 浮层 ---------------- */

	function openSheet(name) {
		var dialog = document.getElementById('sheet-' + name);
		if (!dialog || typeof dialog.showModal !== 'function') {
			return;
		}

		if (dialog.open) {
			return;
		}

		dialog.showModal();
		document.body.style.overflow = 'hidden';

		var input = dialog.querySelector('input[type="search"]');
		if (input) {
			window.setTimeout(function () {
				input.focus();
				input.select();
			}, 80);
		}

		dialog.addEventListener('close', function onClose() {
			document.body.style.overflow = '';
			dialog.removeEventListener('close', onClose);
		});
	}

	document.addEventListener('click', function (event) {
		var trigger = event.target.closest('[data-open]');
		if (trigger) {
			openSheet(trigger.getAttribute('data-open'));
			return;
		}

		var closer = event.target.closest('[data-close]');
		if (closer) {
			var dialog = closer.closest('dialog');
			if (dialog) {
				dialog.close();
			}
		}
	});

	document.addEventListener('keydown', function (event) {
		var tag = (event.target.tagName || '').toLowerCase();
		var typing = tag === 'input' || tag === 'textarea' || event.target.isContentEditable;

		if (event.key === 'Escape') {
			var open = document.querySelector('dialog[open]');
			if (open) {
				open.close();
			}
			return;
		}

		if (typing) {
			return;
		}

		if (event.key === '/' || ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k')) {
			event.preventDefault();
			openSheet('search');
		}
	});

	/* ---------------- 阅读进度 ---------------- */

	var progress = document.querySelector('[data-progress]');
	if (progress) {
		var ticking = false;

		var drawProgress = function () {
			ticking = false;
			var height = document.documentElement.scrollHeight - window.innerHeight;
			var ratio = height > 0 ? window.scrollY / height : 0;
			progress.style.setProperty('--p', Math.min(1, Math.max(0, ratio)).toFixed(4));
		};

		window.addEventListener(
			'scroll',
			function () {
				if (!ticking) {
					ticking = true;
					window.requestAnimationFrame(drawProgress);
				}
			},
			{ passive: true }
		);

		drawProgress();
	}

	/* ---------------- 目录高亮 ---------------- */

	var toc = document.querySelector('[data-toc]');
	if (toc) {
		var links = Array.prototype.slice.call(toc.querySelectorAll('[data-toc-link]'));
		var targets = links
			.map(function (link) {
				return document.getElementById(link.getAttribute('data-toc-link'));
			})
			.filter(Boolean);

		if (targets.length) {
			var activeId = '';
			var lastScroll = -1;

			var syncToc = function () {
				var offset = 120;
				var current = targets[0];

				for (var i = 0; i < targets.length; i++) {
					if (targets[i].getBoundingClientRect().top <= offset) {
						current = targets[i];
					}
				}

				if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 4) {
					current = targets[targets.length - 1];
				}

				if (current.id === activeId) {
					return;
				}

				activeId = current.id;
				links.forEach(function (link) {
					var on = link.getAttribute('data-toc-link') === activeId;
					link.classList.toggle('is-active', on);
					if (on) {
						var box = link.parentElement.parentElement;
						if (box && box.scrollHeight > box.clientHeight) {
							var linkTop = link.offsetTop - box.offsetTop;
							if (linkTop < box.scrollTop || linkTop > box.scrollTop + box.clientHeight - 48) {
								box.scrollTo({ top: Math.max(0, linkTop - box.clientHeight / 2), behavior: 'smooth' });
							}
						}
					}
				});
			};

			window.addEventListener(
				'scroll',
				function () {
					if (window.scrollY === lastScroll) {
						return;
					}
					lastScroll = window.scrollY;
					window.requestAnimationFrame(syncToc);
				},
				{ passive: true }
			);

			syncToc();
		}
	}

	/* ---------------- 复制代码 / 复制链接 ---------------- */

	var copyText = function (text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text);
		}

		return new Promise(function (resolve, reject) {
			var area = document.createElement('textarea');
			area.value = text;
			area.setAttribute('readonly', '');
			area.style.position = 'fixed';
			area.style.opacity = '0';
			document.body.appendChild(area);
			area.select();
			try {
				document.execCommand('copy') ? resolve() : reject();
			} catch (e) {
				reject(e);
			}
			document.body.removeChild(area);
		});
	};

	var COPY_ICON =
		'<svg class="icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="11" height="11" rx="2.4"/><path d="M15 9V6.4A2.4 2.4 0 0 0 12.6 4H6.4A2.4 2.4 0 0 0 4 6.4v6.2A2.4 2.4 0 0 0 6.4 15H9"/></svg>';
	var CHECK_ICON =
		'<svg class="icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.6 4.6 4.6L19 7"/></svg>';

	document.querySelectorAll('.prose pre').forEach(function (pre) {
		var code = pre.querySelector('code');
		if (!code) {
			return;
		}

		var btn = document.createElement('button');
		btn.type = 'button';
		btn.className = 'code-copy';
		btn.innerHTML = COPY_ICON + '<span>复制</span>';
		btn.addEventListener('click', function () {
			copyText(code.innerText).then(
				function () {
					btn.innerHTML = CHECK_ICON + '<span>已复制</span>';
					btn.classList.add('is-done');
					window.setTimeout(function () {
						btn.innerHTML = COPY_ICON + '<span>复制</span>';
						btn.classList.remove('is-done');
					}, 1800);
				},
				function () {
					btn.querySelector('span').textContent = '复制失败';
				}
			);
		});
		pre.appendChild(btn);
	});

	document.querySelectorAll('[data-copy-link]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var label = btn.querySelector('[data-copy-label]');
			copyText(window.location.href).then(
				function () {
					if (label) {
						var original = label.textContent;
						label.textContent = btn.getAttribute('data-copied') || '已复制';
						window.setTimeout(function () {
							label.textContent = original;
						}, 1800);
					}
				},
				function () {}
			);
		});
	});

	var shareBtn = document.querySelector('[data-native-share]');
	if (shareBtn && navigator.share) {
		shareBtn.hidden = false;
		shareBtn.addEventListener('click', function () {
			navigator.share({ title: document.title, url: window.location.href }).catch(function () {});
		});
	}

	/* ---------------- 图片放大 ---------------- */

	var lightbox = null;

	var closeLightbox = function () {
		if (lightbox && lightbox.open) {
			lightbox.close();
		}
	};

	document.addEventListener('click', function (event) {
		var img = event.target.closest('.prose img');
		if (!img || event.target.closest('a')) {
			return;
		}

		if (!lightbox) {
			lightbox = document.createElement('dialog');
			lightbox.className = 'lightbox';
			lightbox.setAttribute('aria-label', '图片预览');
			lightbox.innerHTML =
				'<img alt=""><button type="button" class="lightbox__close" aria-label="关闭">' +
				'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg></button>';
			document.body.appendChild(lightbox);
			lightbox.addEventListener('click', function (e) {
				if (e.target === lightbox || e.target.closest('.lightbox__close')) {
					closeLightbox();
				}
			});
			lightbox.addEventListener('close', function () {
				document.body.style.overflow = '';
			});
		}

		lightbox.querySelector('img').src = img.currentSrc || img.src;
		lightbox.querySelector('img').alt = img.alt || '';
		lightbox.showModal();
		document.body.style.overflow = 'hidden';
	});

	/* ---------------- 回到顶部 ---------------- */

	var toTop = document.querySelector('[data-to-top]');
	if (toTop) {
		var toggleToTop = function () {
			toTop.classList.toggle('is-on', window.scrollY > 620);
		};

		window.addEventListener('scroll', toggleToTop, { passive: true });
		toggleToTop();

		toTop.addEventListener('click', function () {
			window.scrollTo({ top: 0, behavior: reduceMotion.matches ? 'auto' : 'smooth' });
		});
	}

	/* ---------------- 进场动画 ---------------- */

	if (!reduceMotion.matches && 'IntersectionObserver' in window) {
		var revealables = document.querySelectorAll(
			'.page-head, .lead, .stream > .row, .post__head, .post__figure, .post__foot > *'
		);

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-in');
						observer.unobserve(entry.target);
					}
				});
			},
			{ rootMargin: '0px 0px -6% 0px', threshold: 0.01 }
		);

		revealables.forEach(function (el, i) {
			el.setAttribute('data-reveal', '');
			el.style.setProperty('--reveal-delay', Math.min(i, 5) * 55 + 'ms');
			observer.observe(el);
		});

		/* 兜底：四秒后全部显示，避免任何内容被动画藏住 */
		window.setTimeout(function () {
			document.querySelectorAll('[data-reveal]:not(.is-in)').forEach(function (el) {
				el.classList.add('is-in');
			});
		}, 4000);
	}

	/* ---------------- 玻璃跟随光源 ---------------- */

	var rail = document.querySelector('.rail');
	if (rail && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
		var raf = 0;
		var point = { x: 0, y: 0 };

		rail.addEventListener('pointermove', function (event) {
			point.x = event.clientX;
			point.y = event.clientY;

			if (raf) {
				return;
			}

			raf = window.requestAnimationFrame(function () {
				raf = 0;
				var rect = rail.getBoundingClientRect();
				rail.style.setProperty('--mx', ((point.x - rect.left) / rect.width) * 100 + '%');
				rail.style.setProperty('--my', ((point.y - rect.top) / rect.height) * 100 + '%');
			});
		});
	}
})();
