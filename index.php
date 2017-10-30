<?php
    require_once('Random.php');
    require_once('Grafik.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Random</title>
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" media="screen" />        
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>                        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.min.js"></script>
    </head>
    <body>        
        <?php 
            $prikaziGrafik = false;            
            $rand = new Random('3b44bfb9-b00d-49ff-934f-7c83841e35cd');
            //ispravnost se proverava samo ako su parametri ucitani iz POST
            if ($rand->ucitajParametre($_POST) && $rand->proveriIspravnost()) {
                $prikaziGrafik = true;
            }
        ?>                        
        
        <div class="container">
            <h1>Slučajni brojevi</h1>
            <form method="post">
                <div class="form-group <?=$rand->getError('n') ? 'has-error' :''?>">
                    <label for="Random[n]">N:</label>
                    <input class="form-control" name="Random[n]" value="<?=$rand->getParametar('n')?>">
                    <?php if ($rand->getError('n')):?>
                        <div class="help-block"><?=htmlentities($rand->getError('n'))?></div>
                    <?php endif;?>
                </div>
                <div class="form-group <?=$rand->getError('min') ? 'has-error' :''?>">
                    <label for="Random[min]">Min:</label>
                    <input class="form-control" name="Random[min]" value="<?=$rand->getParametar('min')?>">
                    <?php if ($rand->getError('min')):?>
                        <div class="help-block"><?=htmlentities($rand->getError('min'))?></div>
                    <?php endif;?>                    
                </div>
                <div class="form-group <?=$rand->getError('max') ? 'has-error' :''?>">
                    <label for="Random[max]">Max:</label>
                    <input class="form-control" name="Random[max]" value="<?=$rand->getParametar('max')?>">
                    <?php if ($rand->getError('max')):?>
                        <div class="help-block"><?=htmlentities($rand->getError('max'))?></div>
                    <?php endif;?>                    
                </div>                
                <button type="submit" class="btn btn-success">Prikaži grafik</button>
            </form>
            
            <?php if ($prikaziGrafik):?>
                <canvas id="grafik"></canvas>
                <?php 
                    $rand->ucitaj();
                ?>
                <script><?=Grafik::getJs('grafik', $rand)?></script>
            <?php endif;?>
                
        </div>
    </body>
</html>
