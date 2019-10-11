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

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>

        <link rel="stylesheet" href="style.css">
        <link rel="icon" href="image/logo.png" />
        <title>GameMap - Graph</title>
    </head>

    <body>
        <div class="design">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-xl-12">
                        <div id="progressbar" class="progress">
                            <div id="progressvalue" class="progress-bar bg-info" role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-12">
                        <div id="mynetwork"></div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>


<script>
    searchTags();

    var tagListLength;
    async function searchTags() {
        var tagList = [];

        <?php
            $url = 'https://steamspy.com/api.php?request=appdetails&appid='.htmlspecialchars($_GET["id"]);
            $data = file_get_contents($url);
        ?>

        tagList.push(Object.keys(<?php echo $data; ?>.tags));
        
        var gameList = [];

        var url = new URL(window.location.href);
        var gameName = url.searchParams.get("name");
        tagListLength = tagList[0].length;
        if (tagListLength == 0) {
            alert("Can't find similar game with " + gameName + " because, this game doesn't have tags.")
        }
        else if (tagListLength == 1) {
            alert("Only tag " + gameName + " has is " + tagList[0][0].toLowerCase() + " so every game which has this tag similar to this game.")
        }
        else if (tagListLength == 2) {
            alert("Games which similar to " + gameName + " have " + tagList[0][0].toLowerCase() + ", " + tagList[0][1].toLowerCase() + " tags.")
        }
        else {
            for (var i in tagList[0]) {
                await sleep(200);

                $.post("gameTags.php",
                {
                    tag: tagList[0][i].toString().replace(/ /g, '+')
                },
                function(data, status){
                    var jsonData = JSON.parse(data.trim());
                    for (var key in jsonData) {
                        gameList.push(jsonData[key].appid + "," + jsonData[key].name);
                    }
                    
                    getSimilarGames(gameList);
                });
            }
        }
    }
    
    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    var turn = 0;
    function getSimilarGames(gameList) {
        turn++;
        document.getElementById("progressvalue").style.width = (turn * 100) / tagListLength + "%";
        document.getElementById("progressvalue").innerHTML = (turn * 100) / tagListLength + "%";

        var frequency = {};
        var mostSimilarGames = [];
        var averageSimilarGames = [];
        var leastSimilarGames = [];
        for (var i in gameList) {
            frequency[gameList[i]] = (frequency[gameList[i]] || 0) + 1;
        }
        for (var j in frequency) {
            if (frequency[j] > tagListLength / 1.25) {
                mostSimilarGames.push(j);
            }
            else if (frequency[j] > tagListLength / 1.5) {
                averageSimilarGames.push(j);
            } 
            else if (frequency[j] > tagListLength / 2) {
                leastSimilarGames.push(j);
            }
        }
        
        draw(mostSimilarGames, averageSimilarGames, leastSimilarGames);
    }

    function draw(mostSimilarGames, averageSimilarGames, leastSimilarGames) {
        var nodes = new vis.DataSet([]);
        var edges = new vis.DataSet([]);

        var container = document.getElementById('mynetwork');
        var data = { nodes: nodes, edges: edges };
        var options = {};
        var network = new vis.Network(container, data, options);

        var urlLink = new URL(window.location.href);
        for (var game in mostSimilarGames) {
            var gameInfo = mostSimilarGames[game].split(",");
            nodes.add({id:gameInfo[0], label:gameInfo[1]});
            edges.add({from:urlLink.searchParams.get("id").toString(), to:gameInfo[0], label:"most"});
        }
        for (var game in averageSimilarGames) {
            var gameInfo = averageSimilarGames[game].split(",");
            nodes.add({id:gameInfo[0], label:gameInfo[1]});
            edges.add({from:urlLink.searchParams.get("id").toString(), to:gameInfo[0], label:"average"});
        }
        for (var game in leastSimilarGames) {
            var gameInfo = leastSimilarGames[game].split(",");
            nodes.add({id:gameInfo[0], label:gameInfo[1]});
            edges.add({from:urlLink.searchParams.get("id").toString(), to:gameInfo[0], label:"least"});
        }

        network.on('click', function(properties) {
            var nodeGameId = properties.nodes[0];
            if (nodeGameId != undefined) {
                window.open("https://store.steampowered.com/app/" + nodeGameId);
            }
        });

        network.on("oncontext", function(properties) {
            var nodeGameId = properties.nodes[0];
            if (nodeGameId != undefined) {
                window.open("graph.php?id=" + nodes.get(properties.nodes[0])["id"] + "&name=" + nodes.get(properties.nodes[0])["label"]);
            }
            return false;
        });
    }
</script>