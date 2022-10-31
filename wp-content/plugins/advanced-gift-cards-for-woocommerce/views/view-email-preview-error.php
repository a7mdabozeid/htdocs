<style>
.agcfw-ajax-error {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    color: #3c434a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    font-size: 16px;
    line-height: 1.4em;
    text-align: center;
}
</style>

<div class="agcfw-ajax-error">
    <div class="inner">
    <?php echo esc_html($error_msg); ?>
    </div>
</div>