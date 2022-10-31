<div class="estp-tweets-wrapper estp-twitter-layout-1">

<?php if(isset($pos_tab_settings['tab_content']['content_slider']['twitter_feed']['display_twitter_followbtn']) && $pos_tab_settings['tab_content']['content_slider']['twitter_feed']['display_twitter_followbtn']==1){ ?>
    <div class="estp-center-align">
        <a href="<?php echo "https://twitter.com/".$pos_tab_settings['tab_content']['content_slider']['twitter_feed']['twitter_username'];?>" target="_blank" class="estp-follow-link">
            <div class="estp-follow-btn"><i></i>
                <span id="l" class="label">
                    Follow <b><?php echo $username;?></b>
                </span>
            </div>
        </a>
    </div>
<?php } ?>


<?php
if (is_array($tweets)) {

    foreach ($tweets as $tweet) {
?>
        <div class="estp-single-tweet-wrapper">
            <div class="estp-tweet-content">
                <div class="estp-tweet-box">
                    <?php
                    if ($tweet->text) 
                    {
                    ?>
                    <div class="estp-twitter-profile-img">
                        <img src="<?php echo $tweet->user->profile_image_url; ?>">
                    </div>

                    <div class="estp-twitter-username">
                        <a href="http://twitter.com/<?php echo $username; ?>" class="estp-tweet-name" target="_blank"><?php echo $username; ?></a> 
                    </div>

                    <div class="estp-twitter-date-wrapper">
                        <p class="estp-timestamp">
                            <a href="https://twitter.com/<?php echo $username; ?>/status/<?php echo $tweet->id_str; ?>" target="_blank">
                                <?php echo $this->get_date_format($tweet->created_at, $time_format); ?>
                            </a>
                        </p>
                    </div>

                    <?php
                        $the_tweet = ' '.$tweet->text . ' '; //adding an extra space to convert hast tag into links
                        $the_tweet = $this->makeClickableLinks($the_tweet);
                        
                        // i. User_mentions must link to the mentioned user's profile.
                        if (is_array($tweet->entities->user_mentions)) 
                        {
                            foreach ($tweet->entities->user_mentions as $key => $user_mention) 
                            {
                                $the_tweet = preg_replace(
                                        '/@' . $user_mention->screen_name . '/i', '<a href="http://www.twitter.com/' . $user_mention->screen_name . '" target="_blank">@' . $user_mention->screen_name . '</a>', $the_tweet);
                            }
                        }

                        // ii. Hashtags must link to a twitter.com search with the hashtag as the query.
                        if (is_array($tweet->entities->hashtags)) 
                        {
                            foreach ($tweet->entities->hashtags as $hashtag) 
                            {
                                $the_tweet = str_replace(' #' . $hashtag->text . ' ', ' <a href="https://twitter.com/search?q=%23' . $hashtag->text . '&src=hash" target="_blank">#' . $hashtag->text . '</a> ', $the_tweet);
                            }
                        }
                        echo $the_tweet . ' ';
                        ?>
                </div><!--tweet content-->
                        
                <div class="aptf-tweet-actions-wrapper aptf-tweet-actions">
                    <a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $tweet->id_str; ?>" class="aptf-tweet-reply aptf-tweet-action-reply" target="_blank"><i class="fa fa-reply"></i>Reply</a>
                    <a href="https://twitter.com/intent/retweet?tweet_id=<?php echo $tweet->id_str; ?>" class="aptf-tweet-retweet aptf-tweet-action-retweet" target="_blank"><i class="fa fa-retweet"></i>Retweet<?php echo $this->number_format_short($tweet->retweet_count); ?></a>
                    <a href="https://twitter.com/intent/favorite?tweet_id=<?php echo $tweet->id_str; ?>" class="aptf-tweet-fav aptf-tweet-action-favourite" target="_blank"><i class="fa fa-star"></i>Favourite<?php echo $this->number_format_short($tweet->favorite_count); ?></a>
                </div>
            </div>
                    <?php
                    } 
                    else
                    {
                    ?>
                    <p>
                        <a href="http://twitter.com/'<?php echo $pos_tab_settings['tab_content']['content_slider']['twitter_feed']['twitter_username']; ?> " target="_blank"><?php _e('Click here to read ' . $pos_tab_settings['tab_content']['content_slider']['twitter_feed']['twitter_username'] . '\'S Twitter feed', ESTP_DOMAIN); ?></a>
                    </p>
                    <?php
                    }
                    ?>
        </div><!-- single_tweet_wrap-->
<?php
    }
}
?>

</div>


