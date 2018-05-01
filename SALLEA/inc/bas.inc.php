</div>
    <!-- /.container -->

    <div class="container">

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
				 <p class="flex-row center detail"><a href="mentions.php">Mentions légales</a>&nbsp;-&nbsp;<a href="cgv.php">CGV</a></p>

                    <p>Copyright &copy; SALLEA 2018 By Bak</p>
                </div>
            </div>
        </footer>

    </div>
    <!-- /.container -->

    <!-- jQuery -->
    <script src="inc/js/jquery.js"></script>
		<script src="<?php echo RACINE_SITE. 'inc/js/jquery.datetimepicker.full.js';?>"></script>
		<script>
    
// A coller dans le haut ou le bas après le link vers le fichier datetimepicker.js    
    
	$.datetimepicker.setLocale('fr');
	$('#date_arrivee').datetimepicker({
		format: 'd/m/Y H:i',
		minDate: 0,
		allowTimes: ['08:00', '09:00', '10:00', '11:00', '12:00'],
		roundTime: 'floor'
	});
	$('#date_depart').datetimepicker({
		format: 'd/m/Y H:i',
		minDate: '+1970/01/02',
		allowTimes: ['16:00', '17:00', '18:00', '19:00', '20:00'],
		roundTime: 'floor'
	});
	var rangeSlider = function(){
  var slider = $('.range-slider'),
      range = $('.range-slider__range'),
      value = $('.range-slider__value');

	}

// Conversion date arrivee pour BDD avant l'insert en BDD

        $dateA = str_replace('/', '-', $_POST['date_arrivee']);
        $Date_arrivee = date('Y-m-d H:i', strtotime($dateA));
        
        // Conversion date depart pour BDD
        
        $dateD = str_replace('/', '-', $_POST['date_depart']);
        $Date_depart = date('Y-m-d H:i', strtotime($dateD));


</script>

    <!-- Bootstrap Core JavaScript -->
    <script src="inc/js/bootstrap.min.js"></script>
	

</body>

</html>
