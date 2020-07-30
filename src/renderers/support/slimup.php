<?php


class Slimup {
	/**
	 * Render some html into Markdown.
	 */
	public static function render ($text) {
		$get = ["<br />", "<br/>", "<p>", "</p>", "<code>", "</code>", "<em>", "</em>", "<i>", "</i>", "<b>", "</b>", "<strong>", "</strong>"];
		$set = ["\n\n", "\n\n", "\n\n", "\n", "`", "`", "", "_", "_", "_", "_", "**", "**", "**", "**"];

		$text = str_replace($get, $set, $text);

		return $text;
}
