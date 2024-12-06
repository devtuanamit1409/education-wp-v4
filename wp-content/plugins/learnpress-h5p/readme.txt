=== LearnPress - H5P ===
Contributors: thimpress
Donate link:
Tags: elearning, education, course, lms, learning management system
Requires at least: 6.3
Tested up to: 6.4.2
Requires PHP: 7.4
Stable tag: 4.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Changelog ==

= 4.0.3 (2023-12-26) =
~ Tweak: slug link, rewrite rule.
~ Tweak: lp_h5p_submenu_order function.
~ Fixed: evaluate wrong, reason by call $item['item_id'], $item is object LP_User_Item.

= 4.0.2 (2023-10-30) =
~ Fixed: minor bugs.
~ Tweak: slug link, rewrite rule.

= 4.0.2 (2022-11-23) =
~ Compatible PHP 8.1
~ Deprecated: __get on the LP_Assignment class.
~ Replace call array key ['items'] to get_items of LP_Query_List_Table.

= 4.0.1 =
- Fix Evaluate via results of the H5P when finish course.
- Call get_total_item_unassigned from LP.
- Remove results 0 when evaluate course.
- Fix add param 'pass' when evaluate course.
- Check get_course_data is false.
~ Check $course_data->get_item is false.
~ Added: show progress on single course.

= 4.0.0 =
- Compatible with LP4.

= 3.0.1 =
- Fix submit Answer Question in Video H5P.
- Fix debug when empty Question title.
- Add Assessment with H5P passed, H5P and Quizzes passed.

= 3.0.0 =
- Compatible with Learnpress 3.0.0.

