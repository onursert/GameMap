<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <link href="https://fonts.googleapis.com/css?family=Manjari&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="style.css">
        <link rel="icon" href="image/logo.png" />
        <title>GameMap - Find Similar Games on Steam</title>
    </head>

    <body>
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalScrollable" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title">How It Works?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modal-body">
                        <ul>
                            <li>It gets all games' infos on Steam via SteamSpy while "Getting All Games..." is on the screen.</li>
                            <li>When you type a game name to input, it finds games which similar to that name. Found games' name either contains or starts with game name you type.</li>
                            <li>Then if you click one of the found games. It gets tags that game has.</li>
                            <li>After that it gets all games on Steam that has those tags.</li>
                            <li>If a game has same tag a lot, it counts as a similar game.</li>
                            <li>Then draw a graph with similar games.</li>
                        </ul>
                        <ul>
                            <p>Sources and Used Libraries</p>
                            <li><a href="https://getbootstrap.com/">Bootstrap</a></li>
                            <li><a href="https://github.com/visjs/vis-network">Vis.js</a></li>
                            <li><a href="https://steamspy.com/">SteamSpy</a></li>
                            <li><a href="https://github.com/onursert/GameMap">Source Codes</a></li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right"><a data-toggle="modal" data-target="#modal" href=""><img src="image/how.png"></a></div>
        <div class="wrap">
            <h1 class="title text-center">GameMap</h1>
            <div id="spinner" class="d-flex align-items-center">
                <strong class="text-secondary">Getting All Games...</strong>
                <div class="spinner-border text-secondary ml-auto" role="status" aria-hidden="true"></div>
            </div>
            <br>
            <div class="input-group mb-3">
                <input type="text" id="gameName" class="autocomplete form-control" placeholder="Game Name" aria-label="Game Name" aria-describedby="buttonaddon" data-toggle="dropdown" oninput="getGames()">
                <ul id="dropdownSuggestion" class="dropdown-menu"></ul>
            </div>
        </div>
    </body>
</html>

<script>
    var gdata;

    $(document).ready( function() {
        $.post("allGames.php", {}, function(data, status) {
            gdata = JSON.parse(data.trim());
            getGames();
            document.getElementById("spinner").style.visibility = "hidden";
        });
    });

    function getGames() {
        var count = 0;
        var elements = document.getElementsByClassName("dropdown-item");
        while (elements.length > 0) {
            elements[0].parentNode.removeChild(elements[0]);
        }
        for (var key in gdata) {
            if (containsOrStartsWith(gdata[key].name, document.getElementById("gameName").value)) {
                var listItem = document.createElement("li");

                var a = document.createElement('a');
                a.className = "dropdown-item";
                a.innerHTML = gdata[key].name + ", " + gdata[key].appid;
                a.href = "graph.php?id=" + gdata[key].appid + "&name=" + gdata[key].name;
                
                listItem.appendChild(a);
                document.getElementById("dropdownSuggestion").appendChild(listItem);
                count++;
            }
            if (count > 7) {
                return;
            }
        }
    }

    function containsOrStartsWith(strA, strB) {
        strA = strA.toUpperCase().replace(/[^a-zA-Z ]/g, " ");
        strB = strB.toUpperCase().replace(/[^a-zA-Z ]/g, " ");
        if (strA.includes(strB) || strA.startsWith(strB)) {
            return true;
        }
    }
</script>
