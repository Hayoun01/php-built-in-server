<?php

/*

    Generated by {{ name }} vscode extension

    Created 2022
    Author {{ author }}
    Version {{ version }}
    Homepage {{ homepage }}

*/

if (php_sapi_name() == "cli-server") {
    $req = new class {
        public $query;
        public $path;
        public function __construct()
        {
            $this->query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
            $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (substr($this->path, -1) === "/") $this->path = substr($this->path, 0, -1);
            if (strlen($this->path) > 0 && substr($this->path, 0, 1) !== "/") $this->path = "/$this->path";
            else if (empty($this->path)) $this->path = "/";
            if ($this->path === "/") $this->path = "/index.php";
        }
    };

    $pathinfo = (object) pathinfo($req->path);
    if (property_exists($pathinfo, "extension")) {
        if (array_search(strtolower($pathinfo->extension), ["php", "html", "htm"]) !== false) {
            if (strtolower($_SERVER['REQUEST_URI']) === "/.vscode/phpbis-router.php") die;

            ob_start(); ?>
            <script src="https://cdn.socket.io/4.5.3/socket.io.min.js" integrity="sha384-WPFUvHkB1aHA5TDSZi6xtDgkF0wXJcIIxXhC6h8OT8EH3fC5PWro5pWJ1THjcfEi" crossorigin="anonymous"></script>
            <script type="module">
                const name = "{{ name }}";
                const socket = io("ws://localhost:{{ port }}", {
                    auth: {
                        token: "{{ token }}"
                    },
                    withCredentials: true,
                    reconnectionDelay: 2000
                });

                socket.on("connect", () => {
                    console.log(`Connected to ${name} websocket ${socket.id}`);
                });

                socket.io.on("reconnect_attempt", num => {
                    console.debug(`Attempt #${num} to reconnect to ${name}`);
                })

                socket.on("disconnect", reason => {
                    console.warn(`${name} disconnected`);
                });

                socket.on("message", data => {
                    if (data.toLowerCase() === 'refresh') {
                        console.debug(`${name} refresh request recieved`);
                        window.location.reload(true);
                    } else if (data.toLowerCase() === 'kill') {
                        console.debug(`${name} kill request recieved`);
                        socket.disconnect();
                    }
                })
            </script>
            <?php ob_get_flush(); }
    }

    {{ globals }}

    include(str_replace("/", DIRECTORY_SEPARATOR,  __DIR__ . "/.." . $req->path));
}
?>
