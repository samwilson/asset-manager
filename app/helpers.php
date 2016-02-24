<?php

/**
 * Check for the existence of a view.
 *
 * @param string $name The view name.
 */
function view_exists($name)
{
    $viewFactory = view();
    if ($viewFactory instanceof \Illuminate\View\Factory) {
        return $viewFactory->exists($name);
    }
    return false;
}
