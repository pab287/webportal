        </div>
    </div>
</div>
<!-- <div class="footer">
    images of logos here
</div> -->
<script>
    let _tempContentData = <?php echo json_encode($data ?? []); ?>;
    let baseUrl = "<?php echo base_url(); ?>";
</script>
<script src="<?php echo base_url('assets/js/crs/online_reg.js'); ?>"></script>
</body>
<!-- end::Body -->
</html>