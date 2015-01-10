<?php
if (!@include('config.php')) {
    header('Location:setup.php');
}

require_once('layout-headerlg.php');
?>
<h1 class='title'><?php require_once('config.php');echo $wsn;?></h1>
<form id='shortenform' method="POST" action="createurl.php" role="form">
    <input type="text" class="form-control" placeholder="URL" id="url" value="http://" name="urlr" />
    <div id='options'>
        <br />
        <div class="btn-group btn-toggle" data-toggle="buttons">
            <label class="btn btn-primary btn-sm active">
              <input type="radio" name="options" value="p" checked=""> Public
            </label>
            <label class="btn btn-sm btn-default">
              <input type="radio" name="options" value="s"> Secret
            </label>
        </div> <br /><br />
        <br>Customize link: <br><div style='color: green'><h2 style='display:inline'><?php require_once('config.php');echo $wsa;?>/</h2><input type='text' id='custom' title='After entering your custom ending, if the ending is available, enter your long URL into box above and press "Shorten"!' name='custom' /><br>
            <a href="#" class="btn btn-inverse btn-sm" id='checkavail'>Check Availability</a><div id='status'></div></div>
    </div>
    <br><input type="submit" class="btn btn-info" id='shorten' value="Shorten!"/>   <a href="#" class="btn btn-warning" id='showoptions'>Link Options</a>
    <input type="hidden" id="hp" name="hp" value="<?php echo $hp; ?>"/>
</form>
<br><br><div id="tips" class='text-muted'><i class="fa fa-spinner"></i> Loading Tips...</div>
<?php require_once('layout-footerlg.php');
