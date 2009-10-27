
<h2>Déposer un nouveau fichier :</h2> 
<section id="new-file">
<form method="POST" enctype="multipart/form-data" action="<?php echo url_for ('upload') ?>" id="upload-form">
  <input type="hidden" name="APC_UPLOAD_PROGRESS" id="upload-id"  value="<?php echo $upload_id ?>" />
  <div id="file">
    <label for="file">Fichier :</label>
    <div id="input-file"><input type="file" name="file" value="" alt="Fichier à déposer" /></div>
  </div>
  <div id="duration">
    <label for="duration">Durée :</label>
    <select id="select-duration" name="duration" alt="Sélectionnez une durée">
      <option value="3">3 jours</option>
      <option value="4">4 jours</option>
      <option value="5">5 jours</option>
      <option value="6">6 jours</option>
      <option value="7">7 jours</option>
    </select>
  </div>
  <div id="start-from">
    <label for="start-from">Date de début :</label>
    <input type="text" id="input-start-from" name="start-from" value="<?php echo $start_from ?>" alt="Sélectionnez une date de début" />
  </div>
  <div id="comment">
    <label for="comment">Commentaire :</label>
    <input type="text" id="input-comment" name="comment" value="" alt="Ajoutez un commentaire (facultatif)" />
  </div>
  <div id="upload">
    <input type="submit" id="start-upload" name="upload" class="awesome blue large" value="&raquo; Envoyer le fichier" />
    <div id="upload-loading"  style="display: none;"></div>
    <div id="upload-progress" style="display: none;"></div>
  </div>

  <script type="text/javascript">

    // @var interval ID
    var progressChecker = 0;

    /**
     * Function called on form submission
     */
    function onFileUploadStart (data, form, options) {
      console.log ('upload starts...');

      // We alter the request to let the server know that this is a (false) xhr request
      data.push ({name: 'is_xhr', value: true});

      $('#start-upload').hide();
      $("#upload-loading").show ();
      $("#upload-progress").progressBar({
        barImage: 'resources/images/progressbg_green.gif',
        boxImage: 'resources/images/progressbar.gif'
      });

      progressChecker = setInterval (function () {
      $.getJSON('<?php echo url_for ('upload/progress/'.$upload_id) ?>', 
        function(data){
          console.log (data);

          if (data == false) {
            // we assume APC and "apc.rfc1867 = on" is not configured
            clearInterval (progressChecker); // We don't need to call the progress checker again
            return;
          }

          $("#upload-loading").hide ();
          $('#upload-progress').show();

          if (data.done == 1)
            clearInterval (progressChecker); 

          var percentage = Math.floor(100 * parseInt(data.current) / parseInt(data.total));
          $("#upload-progress").progressBar(percentage);
        }
      )}, 750);
    }

    /**
     * Function called once the file has been successfully uploaded
     */
    function onFileUploadEnd (data, status) {
      console.log ('upload ends.');
      clearInterval (progressChecker);
      $('#start-upload').show();
      $("#upload-progress").progressBar(0);
      $('#upload-progress').hide();
      $('#upload-loading').hide();
      $('#upload-id').val (uniqid ()); // APC_UPLOAD_PROGRESS id reset
      console.log (data);

      if (data.status == 'ok') {
        var files = $('ul#files');
        var cssClass = files.children('li:first').hasClass ('odd') ? 'even' : 'odd' ;
   
        files.prepend (
          '<li class="file ' + cssClass + '" style="display: none;">' +
          '  <p class="filename"><a href="#">' + data.filename + '</a></p>' +
          '  <p class="download-counter">Téléchargé 0 fois</p>' +
          '  <p class="availability">disponible du 18 au <b>27 octobre</b></p>' +
          '  <ul class="actions">' +
          '    <li><a href="#" class="send-by-email">Envoyer par email</a></li> ' +
          '    <li><a href="#" class="delete">Supprimer</a></li> ' +
          '    <li><a href="#" class="extend">Rendre disponible un jour de plus</a></li>' +
          '  </ul>' +
          '</li>'
        );
        files.children('li:first').slideDown (500);
      }
      else {
      }
    }

    var ajaxFormOptions = { 
      beforeSubmit: onFileUploadStart, // pre-submit callback 
      success:      onFileUploadEnd,   // post-submit callback 
      resetForm:    true,              // reset the form after successful submit 
      iframe:       true,              // force the form to be submitted using an iframe 
                                       // even if no file has been selected
      dataType: 'json'                 // force response type to JSON
    }; 
 
    $(document).ready (function () {
      $('#upload-form').ajaxForm(ajaxFormOptions);
    });
  </script>

  </form>
</section>

<h2 id="uploaded-files-title">Vos fichiers déjà déposés :</h2>
<section id="uploaded-files">
  <ul id="files">
    <?php $odd = true; foreach ($files as $file): ?>
    <li class="file <?php echo $odd ? 'odd' : 'even'; $odd = ! $odd; ?>">
        <p class="filename"><a href="#"><?php echo $file->file_name ?></a></p>
        <p class="download-counter">Téléchargé <?php echo $file->download_count ?> fois</p>
        <p class="availability">disponible du 18 au <b>27 octobre</b></p>
        <ul class="actions">
          <li><a href="#" class="send-by-email">Envoyer par email</a></li> 
          <li><a href="#" class="delete">Supprimer</a></li> 
          <li><a href="#" class="extend">Rendre disponible un jour de plus</a></li>
        </ul>
      </li>
    <?php endforeach ?>
  </ul>
</section>

