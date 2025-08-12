<div class="menu" id="sidebarMenu">
    @if (session()->has('super_admin_loged'))
        <div class="menu-item" onclick="window.location.href='/admin';">
            <svg width="20" height="20" fill="#54656F" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M10.025 9.975 5.83 14.17a2.828 2.828 0 1 1-4-4l4.195-4.195a4.5 4.5 0 0 1 6.405-4.542l-2.145 2.145a1.5 1.5 0 1 0 2.121 2.121l2.15-2.15a4.5 4.5 0 0 1 .421 2.408 4.5 4.5 0 0 1-4.952 4.018m-.74-2.088-4.87 4.869a.828.828 0 0 1-1.17-1.172l4.868-4.869z" />
            </svg>
            <span>Admin Settings</span>
        </div>
    @endif
    @if (session('user_type') === 'admin' || session('user_type') === 'moderator' || session('user_type') === 'super_admin')
        <div class="menu-item" onclick="window.location.href='/add/group';">
            <svg width="20" height="20" viewBox="0 0 36 36" fill="#54656F">
                <path class="clr-i-solid clr-i-solid-path-1"
                    d="M12 16.14h-.87a8.67 8.67 0 0 0-6.43 2.52l-.24.28v8.28h4.08v-4.7l.55-.62.25-.29a11 11 0 0 1 4.71-2.86A6.6 6.6 0 0 1 12 16.14" />
                <path class="clr-i-solid clr-i-solid-path-2"
                    d="M31.34 18.63a8.67 8.67 0 0 0-6.43-2.52 11 11 0 0 0-1.09.06 6.6 6.6 0 0 1-2 2.45 10.9 10.9 0 0 1 5 3l.25.28.54.62v4.71h3.94v-8.32Z" />
                <path class="clr-i-solid clr-i-solid-path-3"
                    d="M11.1 14.19h.31a6.45 6.45 0 0 1 3.11-6.29 4.09 4.09 0 1 0-3.42 6.33Z" />
                <path class="clr-i-solid clr-i-solid-path-4"
                    d="M24.43 13.44a7 7 0 0 1 0 .69 4 4 0 0 0 .58.05h.19A4.09 4.09 0 1 0 21.47 8a6.53 6.53 0 0 1 2.96 5.44" />
                <circle class="clr-i-solid clr-i-solid-path-5" cx="17.87" cy="13.45" r="4.47" />
                <path class="clr-i-solid clr-i-solid-path-6"
                    d="M18.11 20.3A9.7 9.7 0 0 0 11 23l-.25.28v6.33a1.57 1.57 0 0 0 1.6 1.54h11.49a1.57 1.57 0 0 0 1.6-1.54V23.3l-.24-.3a9.58 9.58 0 0 0-7.09-2.7" />
                <path fill="none" d="M0 0h36v36H0z" />
            </svg>
            <span>Add Group</span>
        </div>
    @endif
    <div class="menu-item" onclick="window.location.href='/settings';">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M11.27 2a2.54 2.54 0 0 0-2.54 2.541c0 .203-.14.475-.476.657a8 8 0 0 0-.305.175c-.334.201-.648.188-.832.083a2.567 2.567 0 0 0-3.493.936L2.919 7.6a2.544 2.544 0 0 0 .935 3.488c.178.102.345.36.337.746a8 8 0 0 0 0 .33c.008.385-.16.644-.337.746a2.544 2.544 0 0 0-.935 3.488l.705 1.21a2.567 2.567 0 0 0 3.493.935c.184-.105.498-.118.832.084q.15.09.305.174c.335.182.476.454.476.657A2.54 2.54 0 0 0 11.27 22h1.46a2.54 2.54 0 0 0 2.54-2.541c0-.203.14-.475.476-.657q.155-.084.305-.174c.334-.202.648-.19.832-.084 1.224.7 2.784.283 3.493-.936l.705-1.209a2.544 2.544 0 0 0-.935-3.488c-.178-.102-.345-.36-.337-.746a8 8 0 0 0 0-.33c-.008-.385.16-.644.337-.746a2.544 2.544 0 0 0 .935-3.488l-.704-1.21a2.567 2.567 0 0 0-3.494-.935c-.184.105-.498.118-.832-.083q-.15-.091-.305-.175c-.335-.182-.476-.454-.476-.657A2.54 2.54 0 0 0 12.73 2zM12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6"
                fill="#54656F" />
        </svg>
        <span>Settings</span>
    </div>
    <div class="menu-item" onclick="window.location.href='/user/attendance';">
        <svg width="20" height="20" viewBox="0 0 24 24">
            <path fill="#54656F"
                d="M7 11c-1.1 0-2-.9-2-2V8c0-1.1.9-2 2-2s2 .9 2 2v1c0 1.1-.9 2-2 2m-2 6.993L9 18c.55 0 1-.45 1-1v-2c0-1.65-1.35-3-3-3s-3 1.35-3 3v2c0 .552.448.993 1 .993M19 18h-6a1 1 0 1 1 0-2h6a1 1 0 1 1 0 2m0-4h-6a1 1 0 1 1 0-2h6a1 1 0 1 1 0 2m0-4h-6a1 1 0 1 1 0-2h6a1 1 0 1 1 0 2" />
            <path fill="#54656F"
                d="M22 2H2C.9 2 0 2.9 0 4v16c0 1.1.9 2 2 2h20c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2m0 17.5c0 .28-.22.5-.5.5h-19c-.28 0-.5-.22-.5-.5v-15c0-.28.22-.5.5-.5h19c.28 0 .5.22.5.5z" />
        </svg>
        <span>Attendance</span>
    </div>
    <div class="menu-item" onclick="toggleTheme()">
        <svg width="20" height="20" viewBox="0 0 24 24"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10m0-2V4a8 8 0 1 1 0 16" fill="#54656F"/></svg>
        <span>Change Theme</span>
    </div>
    <div class="menu-item" onclick="window.location.href='/user/tasks';">
        <svg width="20" height="20" viewBox="0 0 52 52" xml:space="preserve" fill="#54656F">
            <path
                d="m24 7-1.7-1.7c-.5-.5-1.2-.5-1.7 0L10 15.8l-4.3-4.2c-.5-.5-1.2-.5-1.7 0l-1.7 1.7c-.5.5-.5 1.2 0 1.7l5.9 5.9c.5.5 1.1.7 1.7.7s1.2-.2 1.7-.7L24 8.7c.4-.4.4-1.2 0-1.7m24.4 11.4H27.5c-.9 0-1.6-.7-1.6-1.6v-3.2c0-.9.7-1.6 1.6-1.6h20.9c.9 0 1.6.7 1.6 1.6v3.2c0 .9-.7 1.6-1.6 1.6m0 14.3H22.7c-.9 0-1.6-.7-1.6-1.6v-3.2c0-.9.7-1.6 1.6-1.6h25.7c.9 0 1.6.7 1.6 1.6v3.2c0 .9-.7 1.6-1.6 1.6m-35.4 0H9.8c-.9 0-1.6-.7-1.6-1.6v-3.2c0-.9.7-1.6 1.6-1.6H13c.9 0 1.6.7 1.6 1.6v3.2c.1.9-.7 1.6-1.6 1.6M13 47H9.8c-.9 0-1.6-.7-1.6-1.6v-3.2c0-.9.7-1.6 1.6-1.6H13c.9 0 1.6.7 1.6 1.6v3.2c.1.9-.7 1.6-1.6 1.6m35.4 0H22.7c-.9 0-1.6-.7-1.6-1.6v-3.2c0-.9.7-1.6 1.6-1.6h25.7c.9 0 1.6.7 1.6 1.6v3.2c0 .9-.7 1.6-1.6 1.6" />
        </svg>
        <span class="task-label-wrapper">
            <span>Tasks</span>
            @if ($tasks > 0)
            <span class="task-badge">{{ $tasks }}</span>
            @endif
        </span>
    </div>
    <div class="menu-item" onclick="window.location.href='/logout';">
        <svg width="18" fill="#54656F" height="18" viewBox="0 0 52 52" xml:space="preserve">
            <path
                d="M21 48.5v-3c0-.8-.7-1.5-1.5-1.5h-10c-.8 0-1.5-.7-1.5-1.5v-33C8 8.7 8.7 8 9.5 8h10c.8 0 1.5-.7 1.5-1.5v-3c0-.8-.7-1.5-1.5-1.5H6C3.8 2 2 3.8 2 6v40c0 2.2 1.8 4 4 4h13.5c.8 0 1.5-.7 1.5-1.5" />
            <path
                d="M49.6 27c.6-.6.6-1.5 0-2.1L36.1 11.4c-.6-.6-1.5-.6-2.1 0l-2.1 2.1c-.6.6-.6 1.5 0 2.1l5.6 5.6c.6.6.2 1.7-.7 1.7H15.5c-.8 0-1.5.6-1.5 1.4v3c0 .8.7 1.6 1.5 1.6h21.2c.9 0 1.3 1.1.7 1.7l-5.6 5.6c-.6.6-.6 1.5 0 2.1l2.1 2.1c.6.6 1.5.6 2.1 0z" />
        </svg>
        <span>Logout</span>
    </div>
</div>