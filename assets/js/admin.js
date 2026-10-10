/**
 * WPCalibrate Dynamic Date & Time Admin JavaScript
 */
(function ($) {
	'use strict';

	$(function () {
		// Copy shortcode to clipboard
		$(document).on('click', '.wpcal-copy-btn, .wpcal-copy-icon-btn', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var textToCopy = $btn.data('clipboard');

			if (!textToCopy) {
				return;
			}

			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText(textToCopy).then(function () {
					showCopiedFeedback($btn);
				}).catch(function () {
					fallbackCopy(textToCopy, $btn);
				});
			} else {
				fallbackCopy(textToCopy, $btn);
			}
		});

		function fallbackCopy(text, $btn) {
			var $temp = $('<textarea>');
			$('body').append($temp);
			$temp.val(text).select();
			try {
				document.execCommand('copy');
				showCopiedFeedback($btn);
			} catch (err) {
				console.error('Copy fallback failed', err);
			}
			$temp.remove();
		}

		function showCopiedFeedback($btn) {
			var originalHtml = $btn.html();
			var feedbackText = (typeof wpcalDdtAdmin !== 'undefined' && wpcalDdtAdmin.copied) ? wpcalDdtAdmin.copied : 'Copied!';

			$btn.html('<span class="dashicons dashicons-yes"></span> ' + feedbackText).addClass('wpcal-copied-state');
			setTimeout(function () {
				$btn.html(originalHtml).removeClass('wpcal-copied-state');
			}, 2000);
		}

		// Auto-generate preset slug from name if slug is empty
		var $presetName = $('#preset_name');
		var $presetSlug = $('#preset_slug');
		if ($presetName.length && $presetSlug.length) {
			$presetName.on('input', function () {
				if (!$presetSlug.data('manually-edited') && $presetSlug.val() === '') {
					var slug = $presetName.val().toLowerCase()
						.replace(/[^a-z0-9_-]/g, '-')
						.replace(/-+/g, '-')
						.replace(/^-|-$/g, '');
					$presetSlug.val(slug);
				}
			});
			$presetSlug.on('input', function () {
				$presetSlug.data('manually-edited', true);
			});
		}
	});
})(jQuery);
