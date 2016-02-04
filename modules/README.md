AssetManager module system
==========================

This file details how to create modules for AssetManager.

AssetManager uses the Caffeinated Modules package,
so your first port of call should be its documentation:
https://github.com/caffeinated/modules/wiki

Any AssetManager-specific information is kept in this file.

Main menu
---------

Modules can add any markup they want to the main menu
simply by creating a `modules/[ModName]/resources/views/nav.twig` file.
This will then be included at the end of the `<section class="top-bar-section">`
in the site's header.

For example, to add a new menu:

    <ul class="left">
        <li class="has-dropdown">
            <a>Module name</a>
            <ul class="dropdown">
                <li><a href="{{url('module-name/search')}}">Search</a></li>
                <li><a href="{{url('module-name/export')}}">Export</a></li>
            </ul>
        </li>
    </ul>

See http://foundation.zurb.com/sites/docs/top-bar.html for more information.
