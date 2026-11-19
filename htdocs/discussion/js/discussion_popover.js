/* Copyright (C) 2026 Laurent Destailleur <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/*
 * Open and close the popover of the module Discussion from the picto of the top right menu
 * (HTML output by printTopRightMenu() in class/actions_discussion.class.php).
 * The page of messages of the channel is loaded into an iframe on first open only, so the conversation
 * survives when the popover is closed and opened again. It is loaded again only when another channel is selected.
 */
(function () {
	var toggle = document.getElementById("topmenu-discussion-toggle");
	var popover = document.getElementById("topmenu-discussion-popover");
	if (!toggle || !popover) {
		return;
	}
	var body = popover.querySelector(".discussion-popover-body");

	// There is no hook into the group of tools of the top right menu (search, AI assistant, bookmarks, quick add),
	// so the picto output by the hook printTopRightMenu is moved at the end of this group.
	var tools = document.querySelector(".login_block_tools .inline-block");
	var dropdown = document.getElementById("topmenu-discussion-dropdown");
	if (tools && dropdown) {
		tools.appendChild(dropdown);
	}

	// Anchor the popover just under the top menu bar
	function positionPopover() {
		var top = document.getElementById("id-top");
		var anchor = (top ? top.getBoundingClientRect().bottom : 44) + 4;
		popover.style.setProperty("--discussion-popover-top", anchor + "px");
	}

	function closePopover() {
		popover.classList.remove("open");
	}

	// Put the focus into the input area of the discussion, like the AI assistant does when its popover opens
	function focusInput() {
		var iframe = body.querySelector("iframe");
		var input = (iframe && iframe.contentDocument) ? iframe.contentDocument.getElementById("channelmessage") : null;
		if (input) {
			iframe.contentWindow.focus();
			input.focus();
		}
	}

	// Show the messages of a channel into the iframe, and make the link to the full page follow it
	var fullpage = document.getElementById("topmenu-discussion-fullpage");
	function loadChannel(channelid) {
		var iframe = body.querySelector("iframe");
		if (!iframe) {
			iframe = document.createElement("iframe");
			iframe.title = popover.getAttribute("aria-label");
			// The input area exists only once the page is loaded (first open, or another channel selected)
			iframe.addEventListener("load", function () {
				if (popover.classList.contains("open")) {
					focusInput();
				}
			});
			body.appendChild(iframe);
		}
		iframe.src = body.dataset.urlbase + encodeURIComponent(channelid);
		fullpage.href = fullpage.dataset.urlbase + encodeURIComponent(channelid);
		body.dataset.channel = channelid;
	}

	// Choice of the channel (shown only when the user has several channels). The last choice is kept for the
	// session, so it is still selected on the next pages.
	var select = document.getElementById("topmenu-discussion-channel");
	if (select) {
		var storagekey = "discussionTopMenuChannel";
		var remembered = null;
		try {
			remembered = sessionStorage.getItem(storagekey);
		} catch (e) {
			// Storage not available: the channel of the setup is used
		}
		if (remembered && /^\d+$/.test(remembered) && select.querySelector('option[value="' + remembered + '"]')) {
			select.value = remembered;
			body.dataset.channel = remembered;
			fullpage.href = fullpage.dataset.urlbase + remembered;
		}
		select.addEventListener("change", function () {
			try {
				sessionStorage.setItem(storagekey, select.value);
			} catch (e) {
				// Storage not available: the choice is kept for this page only
			}
			loadChannel(select.value);
		});
	}

	toggle.addEventListener("click", function (event) {
		event.preventDefault();
		// position:fixed can be broken by a transformed ancestor, so the popover is moved under <body>
		if (popover.parentNode !== document.body) {
			document.body.appendChild(popover);
		}
		positionPopover();
		if (popover.classList.toggle("open")) {
			if (body.querySelector("iframe")) {
				focusInput();
			} else {
				loadChannel(body.dataset.channel);
			}
		}
	});

	popover.querySelector(".discussion-popover-close").addEventListener("click", function (event) {
		event.preventDefault();
		closePopover();
	});

	// A click inside the iframe does not reach this document, so it never closes the popover
	document.addEventListener("click", function (event) {
		if (popover.classList.contains("open") && !event.target.closest("#topmenu-discussion-toggle") && !event.target.closest("#topmenu-discussion-popover")) {
			closePopover();
		}
	});

	document.addEventListener("keydown", function (event) {
		if (event.key === "Escape") {
			closePopover();
		}
	});
})();
