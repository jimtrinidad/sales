    <script type="text/javascript">
            swfobject.embedSWF(
              "assets/swf/open-flash-chart.swf", "graphresult",
              "<?= $chart_width ?>", "<?= $chart_height ?>",
              "9.0.0", "expressInstall.swf",
              {"data-file":"<?= urlencode($data_url) ?>","loading":"Loading... Please wait.."},{"wmode":"transparent"}
            );
            function save_image() { OFC.jquery.popup('graphresult'); }
    </script>	
    <div id="graphresult"></div>