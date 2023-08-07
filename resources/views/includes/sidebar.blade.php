@php

function renderSubMenu($value, $currentUrl)
{
    $isActiveChildUrl = false;
    $isParentActive = false;
    $subMenu = '';
    foreach ($value as $key => $menu) {
        $subSubMenu = '';
        $hasTitle = !empty($menu['title']) ? $menu['title'] : '';
        $hasSubTitle = !empty($menu['subTitle']) ? $menu['subTitle'] : '';
        $hasIcon = !empty($menu['icon']) ? '<i class="fa '. $menu['icon'] .'"></i>' : '';

        $isActiveChildUrl = $currentUrl == $menu['url'];
        if ($isActiveChildUrl && !$isParentActive) {
            $isParentActive = true;
        }
        $active = $isActiveChildUrl ? 'nav-active' : '';
        $subMenu .=
            '<li class="nav-item '.$active.'">
            <a class="nav-link" href="' .$menu['url'] .'"
                title="' .$hasSubTitle .'" >' .$hasIcon.' '.
            $hasTitle .
            '</a></li>';
    }
    return ['subMenu' => $subMenu, 'parentActive' => $isParentActive];
}

function renderMenu()
{
    $return = '';
    $currentUrl = Request::path();
    foreach (config('sidebar.menu') as $key => $menu) {
        $isActive = $currentUrl == $menu['url'];
        $menu['url'] = URL::to($menu['url']);
        if (!in_array(Session::get('icfms_tipe_login'), $menu['akses'])) {
            continue;
        }

        $idMenu = 'navbar-' . str_replace(' ', '', $menu['title']);
        $hasSub = !empty($menu['sub_menu']) ? 'nav-parent' : '';
        $isParent = !empty($menu['sub_menu']) ? 'data-toggle="collapse" role="button" ' . 'aria-expanded="false" aria-controls="'.$idMenu.'" ' : '';
        $hasIcon = !empty($menu['icon']) ? '<i class="fa '. $menu['icon'] .'"></i>' : '';
        $hasTitle = !empty($menu['title']) ? '<span>' . $menu['title'] . '</span>' : '';
        $hasSubTitle = !empty($menu['subTitle']) ? $menu['subTitle'] : '';

        $subMenu = '';
        $isParentActive = false;
        $show = '';
        if (!empty($menu['sub_menu'])) {
            $render = renderSubMenu($menu['sub_menu'], $currentUrl, $menu['title']);
            $isParentActive = $render['parentActive'];
            $show = $isParentActive ? 'nav-expanded' : '';
            $subMenu .= '<ul class="nav nav-children">';
            $subMenu .= $render['subMenu'];
            $subMenu .= '   </ul>';
            $menu['url'] = '#' . $idMenu;
        }

        $activeText = $isActive ? 'nav-active' : '';

        $return .=
            '<li class="'. $activeText.' '.$hasSub.' '.$show.'">
            <a title="' .$hasSubTitle .'" href="' .$menu['url'] .'">' .
            $hasIcon .
            '' .
            $hasTitle .
            '</a>' .
            $subMenu .
            '</li>';
    }
    return $return;
}
@endphp
<div class="sidebar-header">
    <div class="sidebar-title">
        &nbsp;
    </div>
    <div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html"
        data-fire-event="sidebar-left-toggle">
        <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
    </div>
</div>

<div class="nano">
    <div class="nano-content">
        <nav id="menu" class="nav-main" role="navigation">
            <ul class="nav nav-main">
                @php echo renderMenu(); @endphp
{{--
                <li class="nav-parent">
                    <a>
                        <i class="fa fa-copy" aria-hidden="true"></i>
                        <span>Pages</span>
                    </a>
                    <ul class="nav nav-children">
                        <li>
                            <a href="pages-signup.html">
                                Sign Up
                            </a>
                        </li>
                        <li>
                            <a href="pages-signin.html">
                                Sign In
                            </a>
                        </li>
                        <li>
                            <a href="pages-recover-password.html">
                                Recover Password
                            </a>
                        </li>
                        <li>
                            <a href="pages-lock-screen.html">
                                Locked Screen
                            </a>
                        </li>
                        <li>
                            <a href="pages-user-profile.html">
                                User Profile
                            </a>
                        </li>
                        <li>
                            <a href="pages-session-timeout.html">
                                Session Timeout
                            </a>
                        </li>
                        <li>
                            <a href="pages-calendar.html">
                                Calendar
                            </a>
                        </li>
                        <li>
                            <a href="pages-timeline.html">
                                Timeline
                            </a>
                        </li>
                        <li>
                            <a href="pages-media-gallery.html">
                                Media Gallery
                            </a>
                        </li>
                        <li>
                            <a href="pages-invoice.html">
                                Invoice
                            </a>
                        </li>
                        <li>
                            <a href="pages-blank.html">
                                Blank Page
                            </a>
                        </li>
                        <li>
                            <a href="pages-404.html">
                                404
                            </a>
                        </li>
                        <li>
                            <a href="pages-500.html">
                                500
                            </a>
                        </li>
                        <li>
                            <a href="pages-log-viewer.html">
                                Log Viewer
                            </a>
                        </li>
                        <li>
                            <a href="pages-search-results.html">
                                Search Results
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-parent">
                    <a>
                        <i class="fa fa-tasks" aria-hidden="true"></i>
                        <span>UI Elements</span>
                    </a>
                    <ul class="nav nav-children">
                        <li>
                            <a href="ui-elements-typography.html">
                                Typography
                            </a>
                        </li>
                        <li class="nav-parent">
                            <a>
                                Icons
                            </a>
                            <ul class="nav nav-children">
                                <li>
                                    <a href="ui-elements-icons-elusive.html">
                                        Elusive
                                    </a>
                                </li>
                                <li>
                                    <a href="ui-elements-icons-font-awesome.html">
                                        Font Awesome
                                    </a>
                                </li>
                                <li>
                                    <a href="ui-elements-icons-glyphicons.html">
                                        Glyphicons
                                    </a>
                                </li>
                                <li>
                                    <a href="ui-elements-icons-line-icons.html">
                                        Line Icons
                                    </a>
                                </li>
                                <li>
                                    <a href="ui-elements-icons-meteocons.html">
                                        Meteocons
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="ui-elements-tabs.html">
                                Tabs
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-panels.html">
                                Panels
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-widgets.html">
                                Widgets
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-portlets.html">
                                Portlets
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-buttons.html">
                                Buttons
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-alerts.html">
                                Alerts
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-notifications.html">
                                Notifications
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-modals.html">
                                Modals
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-lightbox.html">
                                Lightbox
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-progressbars.html">
                                Progress Bars
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-sliders.html">
                                Sliders
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-carousels.html">
                                Carousels
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-accordions.html">
                                Accordions
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-nestable.html">
                                Nestable
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-tree-view.html">
                                Tree View
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-scrollable.html">
                                Scrollable
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-grid-system.html">
                                Grid System
                            </a>
                        </li>
                        <li>
                            <a href="ui-elements-charts.html">
                                Charts
                            </a>
                        </li>
                        <li class="nav-parent">
                            <a>
                                Animations
                            </a>
                            <ul class="nav nav-children">
                                <li>
                                    <a href="ui-elements-animations-appear.html">
                                        Appear
                                    </a>
                                </li>
                                <li>
                                    <a href="ui-elements-animations-hover.html">
                                        Hover
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-parent">
                            <a>
                                Loading
                            </a>
                            <ul class="nav nav-children">
                                <li>
                                    <a href="ui-elements-loading-overlay.html">
                                        Overlay
                                    </a>

                                </li>
                                <li>
                                    <a href="ui-elements-loading-progress.html">
                                        Progress
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="ui-elements-extra.html">
                                Extra
                            </a>
                        </li>
                    </ul>
                </li> --}}
            </ul>
        </nav>

        <hr class="separator" />
    </div>

</div>
