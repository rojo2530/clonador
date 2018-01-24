<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Clona tu Wordpress</title>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <!--     Fonts and icons     -->
    <link href="https://netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">
    <!-- CSS Files -->
    <link href="assets/css/gsdk-bootstrap-wizard.css" rel="stylesheet" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="assets/css/demo.css" rel="stylesheet" />
    <!--   Core JS Files   -->
    <script src="assets/js/jquery-1.10.2.js" type="text/javascript"></script>

    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/js/jquery.bootstrap.wizard.js" type="text/javascript"></script>

    <!--  Plugin for the Wizard -->
    <script src="assets/js/gsdk-bootstrap-wizard.js"></script>

    <!--  More information about jquery.validate here: http://jqueryvalidation.org/	 -->
    <script src="assets/js/jquery.validate.min.js"></script>
</head>

<div id="deleteError" class="alert alert-danger">
    <span class="glyphicon glyphicon-remove-sign"></span>
    <div class="alert-message">
        There was a problem removing the subdomain “weclon.xphera.es”.
        <div>
            subdomain &#39;weclon.xphera.es&#39; does not exist for user &#39;xphera&#39;
        </div>
    </div>
</div>
<div class="image-container set-full-height">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <!--      Wizard container        -->
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="red" id="wizardProfile">
                        <form action="" method="">
                            <div class="wizard-header">
                                <h3>
                                    <b>INTRODUCE</b> TUS DATOS <br>
                                    <small>Con tan solo son 3 clicks tendrás tu Wordpress clonado.</small>
                                </h3>
                            </div>
                            <div class="wizard-navigation">
                                <ul>
                                    <li><a href="#about" data-toggle="tab" style="pointer-events: none;">Paso 1</a></li>
                                    <li><a href="#account" data-toggle="tab" style="pointer-events: none;">Paso 2</a></li>
                                    <li><a href="#address" data-toggle="tab" style="pointer-events: none;">Paso 3</a></li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane" id="about">
                                    <div class="row">
                                        <h4 class="info-text"> Selecciona el dominio donde tienes el Wordpress que quieres clonar</h4>
                                        <div class="col-sm-4 col-sm-offset-1">
                                            <div class="picture-container">

                                            </div>
                                        </div>
                                        <div class="col-sm-6">

                                        </div>
                                        <div class="col-sm-10 col-sm-offset-1">
                                            <div class="form-group">
                                                <label>Dominios con Wordpress <small>(campo obligatorio)</small></label>
                                                <select name="selector-dominios" id="dominio_sel" required class="form-control">
                                                    <option value="">Selecciona tu dominio..</option>
                                                <?php foreach ($domainsWp as $dominio) : ?>
                                                    <option value='<?= $dominio ?>'><?= $dominio ?></option>
                                                <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="account">
                                    <div class="row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <div class="col-sm-10">
                                                <div class="choice" data-toggle="wizard-checkbox">
                                                    <br />
                                                    <div id="check1">
                                                        <span id="addErrorImg"></span>
                                                    </div>
                                                    <div id="resumen"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="address">
                                    <div class="row">
                                        <div class="col-sm-12" id="tabclon" align="center">

                                        </div>
                                        <div class="col-sm-3">
                                        </div>
                                        <div class="col-sm-5 col-sm-offset-1">
                                        </div>
                                        <div class="col-sm-5">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wizard-footer height-wizard">
                                <div class="pull-right">
                                    <input type='button' class='btn btn-next btn-fill btn-warning btn-wd btn-sm' id='siguiente' disabled  name='next' value='Siguiente' />
                                    <input type="button" class="btn btn-finish btn-fill btn-warning btn-wd btn-sm" id='clonar' disabled dname="finish" value="Clonar" style="display: none;">
                                </div>
                                <div class="pull-left">
                                    <input type='button' id='anterior' class='btn btn-previous btn-fill btn-default btn-wd btn-sm' name='previous' value='Anterior' />
                                </div>
                                <div class="clearfix"></div>
                            </div>

                        </form>
                    </div>
                </div> <!-- wizard container -->
            </div>
        </div><!-- end row -->
    </div> <!--  big container -->
</div>
</html>
