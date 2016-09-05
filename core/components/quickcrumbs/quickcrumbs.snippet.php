<?php

/**
 * QuickCrumbs
 *
 * Copyright 2010-2012 by MODx, LLC
 *
 * QuickCrumbs is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * QuickCrumbs is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * QuickCrumbs; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package quickcrumbs
 */
/**
 * A quick and efficient bread crumbs Snippet for MODx Revolution.
 *
 * @package quickcrumbs
 */
$output = array();
$parentTitles = array();
$resourceId = (integer)$modx->getOption('resourceId', $scriptProperties, 0);
if (!$resourceId) $resourceId = (integer)$modx->resource->get('id');
$fields = empty($fields) ? 'pagetitle,menutitle,description' : $fields;
$fields = explode(',', $fields);
foreach ($fields as $fieldKey => $field) $fields[$fieldKey] = trim($field);
array_unshift($fields, 'id', 'class_key', 'context_key');
$tvs = empty($tvs) ? '' : $tvs;
$tvs = explode(',', $tvs);
foreach ($tvs as $tvKey => $tv) $tvs[$tvKey] = trim($tv);
$parents = $modx->getParentIds($resourceId);
array_pop($parents);
$parents = array_reverse($parents);
array_push($parents, $resourceId);
$siteStart = (integer)$modx->getOption('site_start', null, 1);

//Убираем активную если есть добавление из другого сниппета
$addcrumb = $modx->getOption('addcrumb', $scriptProperties, '');
$addcrumb_on = false;
if ($addcrumb) {
    $addcrumb = array_map('trim', explode(',', $addcrumb));
    foreach ($addcrumb as $acrumb) {
        if (!empty($_GET[$acrumb])) {
            $addcrumb_on = true;
        }
    }
}

if (!in_array($siteStart, $parents) && !empty($showSiteStart)) {
    array_unshift($parents, $siteStart);
}
if (empty($siteStartTpl)) {
    $siteStartTpl = $tpl;
}
if (empty($selfTpl)) {
    $selfTpl = $tpl;
}
if ($siteStart == $resourceId && !empty($showSelf)) {
    $siteStartTpl = $selfTpl;
}

if (!empty($parents)) {
    $query = $modx->newQuery('modResource', array('id:IN' => $parents, 'published' => 1, 'deleted' => 0));
    if (!empty($excludeHidden)) {
        $query->where(array('hidemenu' => 0));
    }
    if (!empty($hideEmptyContainers)) {
        $query->where(array(
            'content:!=' => '',
            'class_key:NOT IN' => array('modWebLink', 'modSymLink')
        ));
    }
    if (!empty($hideIds)) {
        $query->where(array('id:NOT IN' => explode(',', $hideIds)));
    }
    $query->select($modx->getSelectColumns('modResource', '', '', $fields));
    $collection = $modx->getCollection('modResource', $query);
    $top = true;
    $crumb = 1;
    $maxCrumbs = !empty($maxCrumbs) ? (integer)$maxCrumbs : 0;
    $totalCrumbs = count($parents);
    $skip = $maxCrumbs > 0 ? ($totalCrumbs - $maxCrumbs - 2) : 0;
    $parent = reset($parents);
    while ($parent) {
        $object = reset($collection);
        while ($object && (integer)$object->get('id') !== (integer)$parent) {
            $object = next($collection);
        }
        if ($object) {
            $properties = array_merge($scriptProperties, $object->get($fields));
            foreach ($tvs as $tvKey => $tv) {
                $properties = array_merge($properties, array('tv.' . $tv => $object->getTVValue($tv)));
            }
            $self = $object->get('id') === $resourceId;
            $skipped = false;
            if (!$top && !$self && $skip > 0) {
                if ($skipFromTop) {
                    if (!empty($skipTpl)) {
                        $output[] = $modx->getChunk($skipTpl, $properties);
                    }
                    $skipped = true;
                    $skip--;
                } elseif ($totalCrumbs - $crumb <= $skip) {
                    if (!empty($skipTpl)) {
                        $output[] = $modx->getChunk($skipTpl, $properties);
                    }
                    $skipped = true;
                    $skip--;
                }
            }
            if (!$skipped) {
                if ((integer)$parent == $siteStart && !empty($showSiteStart)) {
                    if (!empty($siteStartTpl)) {
                        $output[] = $modx->getChunk($siteStartTpl, $properties);
                    } else {
                        $output[] = "<pre>" . print_r($properties, true) . "</pre>";
                    }
                } elseif ($self && !empty($showSelf) && !$addcrumb_on) {
                    if (!empty($selfTpl)) {
                        $output[] = $modx->getChunk($selfTpl, $properties);
                    } else {
                        $output[] = "<pre>" . print_r($properties, true) . "</pre>";
                    }
                } else {
                    $parentTitles[] = $properties['pagetitle'];
                    if (!empty($tpl)) {
                        $output[] = $modx->getChunk($tpl, $properties);
                    } else {
                        $output[] = "<pre>" . print_r($properties, true) . "</pre>";
                    }
                }
            }
        }
        $parent = next($parents);
        $crumb++;
        $top = false;
    }
}
$separator = isset($separator) ? "{$separator}" : "&nbsp;&raquo;&nbsp;";
$output = implode($separator, $output);
if (!empty($outerTpl)) {
    $output = $modx->getChunk($outerTpl, array('crumbs' => $output));
}
if (!empty($parentTitlesPlaceholder) && !empty($parentTitles)) {
    if (empty($titleSeparator)) {
        $titleSeparator = ' - ';
    }
    if (!empty($parentTitlesReversed)) {
        $parentTitles = array_reverse($parentTitles);
    }
    $parentTitles = implode($titleSeparator, $parentTitles);
    $modx->setPlaceholder($parentTitlesPlaceholder, $titleSeparator . $parentTitles);
}
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
    return '';
} else {
    return $output;
}