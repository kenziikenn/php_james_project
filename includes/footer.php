<?php
if(!defined('FOOTER_ACCESS')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Access Denied');
}

// Prevent output buffering manipulation
if (ob_get_level()) ob_end_clean();

// Force footer display
function enforceFooter() {
    $footer = '<footer style="
        position: fixed;
        left: 270px;
        bottom: 0;
        width: calc(100% - 270px);
        background: #0d1b2a;
        color: #d1d1e1;
        text-align: center;
        padding: 14px 0;
        font-size: 0.95rem;
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.2);
        z-index: 99999;
        border-top: 1px solid rgba(233, 69, 96, 0.1);
    ">
        &copy; 2025 Created by James Carl Acaso. All Rights Reserved.
    </footer>';
    echo $footer;
}

register_shutdown_function('enforceFooter');
?>