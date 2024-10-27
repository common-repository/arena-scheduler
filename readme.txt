=== Arena Scheduler ===
Contributors: LEVEL5
Donate link: https://level5.se/
Tags: arena, schedule, calendar
Requires at least: 7.0
Tested up to: 6.6
Requires PHP: 8.0
Stable tag: 1.0.10
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An easy and professional way to organize and schedule arena activities.

== Description ==

Arena Scheduler allows users to manage and display arena booking schedules through a fully responsive calendar. Designed for sports complexes, event managers, and other venues, the plugin provides an intuitive interface for both admins and users. Easily manage bookings, show availability, and customize categories with the Pro version.

== Installation ==

= Uploading via WordPress Dashboard =

1. Navigate to the "Add New" in the plugins dashboard
2. Navigate to the "Upload" area
3. Select `arena-scheduler.zip` from your computer
4. Click "Install Now"
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `arena-scheduler.zip`
2. Extract the `arena-scheduler` directory on your computer
3. Upload the `arena-scheduler` directory to the `/wp-content/plugins/` directory
4. Activate it from the Plugins dashboard

== Usage ==

To display the arena scheduler on a page or post, use the following shortcode:

`[arena_scheduler_view_calendar]`

Simply add this shortcode to any page or post where you want the scheduler to appear.

== BASIC FEATURES ==

- Display arena availability with a responsive schedule
- Add one arena
- Add up to two categories for bookings

== PRO FEATURES ==

- Add unlimited arenas
- Add unlimited categories
- Customize category colors
- Set a default arena
- Activate or deactivate specific arenas and categories

== External Resources ==

This plugin utilizes the following external resources:

This plugin uses the following external resources:

- Google Fonts: The "Inter" font from Google Fonts is used for enhanced visual presentation. It is licensed under the SIL Open Font License, which is GPL-compatible.
- Bootstrap 5.3: Provides responsive layout and design elements. Bootstrap documentation
- DataTables 2.1.4: Included for advanced table features. DataTables documentation
- jQuery Toast Plugin: Used for notifications. GitHub repository
- React App: A React-based front-end powers dynamic scheduling features.

== Frequently Asked Questions ==

= Does this plugin include Bootstrap? =
Yes, Bootstrap 5.3 is included, along with uncompiled CSS and JavaScript files.

= Does this plugin include DataTables? =
Yes, it includes Bootstrap DataTables 2.1.4, with non-compiled versions of the files.

= Does this plugin include the jQuery Toast Plugin? =
Yes, the jQuery Toast Plugin is included with non-compiled files.

= Does this plugin include a React app? =
Yes, a React app is used for the frontend, and all source files are included within the plugin.


== Upgrade Notice ==
None.

== Changelog ==

= 1.0.10 =
- Added uncompressed CSS and JS files, and implemented security updates.

= 1.0.9 =
- CDN Libraries Localized: All external CDN libraries have been moved to be stored locally within the plugin. This reduces dependency on external servers and enhances the plugin's reliability and performance.

= 1.0.8 =
- This release includes bug fixes and security updates.

= 1.0.7 =
- Enhanced design for Admin Arena and Activities.
- Added Knowledge Base and Open a Ticket pages.

= 1.0.6 =
- Resolved warnings related to query and request data, ensuring adherence to WordPress coding standards.
- Enhanced security by properly escaping all database queries.
- Updated request handling to comply with WordPress standards, preventing potential issues and improving compatibility.





