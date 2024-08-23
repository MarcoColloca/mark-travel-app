<?php

if (!function_exists('formatItalianDateHour')) {
    function formatItalianDateHour($date) {
        return \Carbon\Carbon::parse($date)->format('d-m-Y') . ' alle ' . \Carbon\Carbon::parse($date)->format('H:i');
    }
}

if (!function_exists('formatItalianDate')) {
    function formatItalianDate($date) {
        return \Carbon\Carbon::parse($date)->format('d/m/Y');
    }
}