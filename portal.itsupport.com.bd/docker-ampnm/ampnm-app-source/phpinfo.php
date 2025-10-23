<?php
phpinfo();

echo '<h2>Command Execution Test</h2>';

// Function to check if a function is disabled
function is_function_disabled($function_name) {
    $disabled_functions = explode(',', ini_get('disable_functions'));
    return in_array($function_name, $disabled_functions);
}

// Test exec and shell_exec
echo '<h3>PHP Function Status:</h3>';
echo '<p><strong>exec() enabled:</strong> ' . (is_function_disabled('exec') ? '<span style="color: red;">No</span>' : '<span style="color: green;">Yes</span>') . '</p>';
echo '<p><strong>shell_exec() enabled:</strong> ' . (is_function_disabled('shell_exec') ? '<span style="color: red;">No</span>' : '<span style="color: green;">Yes</span>') . '</p>';

if (!is_function_disabled('exec') && !is_function_disabled('shell_exec')) {
    echo '<h3>Basic Command Test:</h3>';
    echo '<p><strong>exec("echo Hello World"):</strong> ';
    $output_exec = [];
    $return_var_exec = 0;
    exec('echo Hello World', $output_exec, $return_var_exec);
    echo ($return_var_exec === 0 ? '<span style="color: green;">Success</span>' : '<span style="color: red;">Failed</span>') . ' (Output: ' . htmlspecialchars(implode(', ', $output_exec)) . ')</p>';

    echo '<p><strong>shell_exec("echo Hello World"):</strong> ';
    $output_shell_exec = shell_exec('echo Hello World');
    echo ($output_shell_exec !== null ? '<span style="color: green;">Success</span>' : '<span style="color: red;">Failed</span>') . ' (Output: ' . htmlspecialchars($output_shell_exec) . ')</p>';

    echo '<h3>Ping Command Test:</h3>';
    echo '<p><strong>which ping:</strong> ';
    $which_ping = shell_exec('which ping 2>&1');
    $ping_found = (strpos((string)$which_ping, 'ping') !== false); // Cast to string to avoid deprecation
    echo ($ping_found ? '<span style="color: green;">Found</span>' : '<span style="color: red;">Not Found</span>') . ' (Path: ' . htmlspecialchars((string)$which_ping) . ')</p>'; // Cast to string

    echo '<p><strong>ping -c 1 127.0.0.1:</strong> ';
    $ping_output = shell_exec('ping -c 1 127.0.0.1 2>&1');
    echo (strpos((string)$ping_output, '1 received') !== false ? '<span style="color: green;">Success</span>' : '<span style="color: red;">Failed</span>') . ' (Output: <pre>' . htmlspecialchars((string)$ping_output) . '</pre>)</p>'; // Cast to string

    echo '<h3>Nmap Command Test:</h3>';
    echo '<p><strong>which nmap:</strong> ';
    $which_nmap = shell_exec('which nmap 2>&1');
    $nmap_found = (strpos((string)$which_nmap, 'nmap') !== false); // Cast to string
    echo ($nmap_found ? '<span style="color: green;">Found</span>' : '<span style="color: red;">Not Found</span>') . ' (Path: ' . htmlspecialchars((string)$which_nmap) . ')</p>'; // Cast to string

} else {
    echo '<p style="color: red;">`exec()` or `shell_exec()` are disabled. Command execution tests cannot be performed.</p>';
}
?>