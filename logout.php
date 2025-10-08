<script>
    alert("Your Session was timed Out! Please Login Again");
</script>

<?php
    session_start();
    
    session_destroy();

    header("Location: index.php");
?>