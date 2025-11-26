<?php
function table_date($datetime)
{
    try {
        $date = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $datetime);
        if (!$date) {
            // Try another common format if the first fails
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        }
        if ($date instanceof DateTime) {
            return $date->format('M d, Y');
        } else {
            return 'Invalid datetime';
        }
    } catch (Exception $e) {
        return 'Error parsing datetime';
    }
}


function end_url()
{
    return url('/api') . '/';
}

function user_roles($role_no)
{
    switch ($role_no) {
        case 1:
            return 'Admin';
        case 2:
            return 'User';
        default:
            return false;
    }
}

function auth_users()
{
    // status : 1 for active , 2 for pending, 3 for suspended , 4 for unverified ,5 for delete ...
    $user_status =  [1, 2];
    return $user_status;
}

function active_users()
{
    // status : 1 for active , 2 for pending, 3 for suspended , 4 for unverified ,5 for delete ...
    $user_status =  [1];
    return $user_status;
}

function user_role_no($role_no)
{
    switch ($role_no) {
        case 'Admin':
            return 1;
        case 'User':
            return 2;
        default:
            return false;
    }
}

function view_permission($page_name)
{
    $user_role = Auth()->User()->role;
    switch ($user_role) {
        case 'Admin':
            switch ($page_name) {
                case 'index':
                case 'users':
                    return true;
                default:
                    return false;
            }

        case 'User':
            switch ($page_name) {
                case 'index':
                    return true;
                default:
                    return false;
            }
        default:
            return false;
    }
}
