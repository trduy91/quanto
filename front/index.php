<!-- HTMLコード -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QUANTO</title>

    <!-- css -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="./assets/css/style.css">


    <!-- javascript -->
<!--    <script src="./libs/jquery/jquery-3.4.1.min.js"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjs/5.0.0/math.js"></script>


</head>

<?php
$survey_id = (isset($_GET['id'])) ? $_GET['id'] : -1;
?>

<body>
    <header id="header" class="header">
        <div class="site-header">
            <div class="site-header-inner">
                <div class="brand-wrapper">
                    <div id="brand" class="brand"><img src="" alt="" /></div>
                    <p id="brand-name" class="brand-name"></p>
                </div>
                <div id="brand-desc" class="brand-desc"></div>
                <div id="title-desc" class="title-desc">
                    <h1 id="title" class="title"><span></span></h1>
                    <p id="description" class="description"></p>
                </div>
                <div id="btn-start" class="btn-start">START</div>
                <div id="progress-row" class="progress-row">
                    <div class="point"></div>
                    <div id="progress-inner" class="progress-inner"></div>
                    <div class="point"></div>
                </div>
            </div>
        </div>
    </header>
    <div id="content" class="content">
        <form action="http://formstylee-admin.com/api/v1/client/save" method="POST">
            <input type="hidden" name="survey_id" value="<?=$survey_id;?>" />
            <div id="survey" class="survey">
            </div>

        </form>
    </div>
    <div id="loading-area">
        <div class="loader-wrapper">
            <div class="loader">Loading...</div>
        </div>
    </div>
    <script type="text/javascript">
        var survey_id = '<?=$survey_id;?>';
    </script>
    <script src="./assets/js/script.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
</body>
</html>