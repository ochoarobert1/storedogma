<?php
if (!defined('ABSPATH')) {
    exit;
}

class Dogma_Admin_Page
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu()
    {
        add_menu_page(
            'Dogma Email Sender',
            'Email Sender',
            'manage_options',
            'dogma-email-sender',
            array($this, 'admin_page'),
            'dashicons-email-alt',
            30
        );
    }

    public function admin_page()
    {
        $products = wc_get_products(array('limit' => -1,  'stock_status' => 'instock'));

?>
        <div class="wrap">
            <div class="header-wrap">
                <img src="<?php echo DogmaEmailSender::get_plugin_url() . 'assets/img/logo-email.png'; ?>" alt="Logo" />
                <h1>Dogma Email Sender</h1>
            </div>


            <form id="email-sender-form">
                <table class="form-table">
                    <tr>
                        <th><label for="product_select">Select Meet and Greet:</label></th>
                        <td>
                            <select id="product_select" name="product_id" required>
                                <option value="">Choose a Meet and Greet...</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product->get_id(); ?>">
                                        <?php echo $product->get_name(); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="customer-emails-row" style="display:none;">
                        <th><label for="customer_emails">Customer Emails:</label></th>
                        <td>
                            <input type="text" id="customer_emails" name="customer_emails" class="large-text" readonly>
                            <p class="description"><span id="email-count">0</span> emails found</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="email_subject">Email Subject:</label></th>
                        <td><input type="text" id="email_subject" name="subject" class="regular-text" required value="<?php echo esc_html($this->get_template_title_email()); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="email_message">Email Message:</label></th>
                        <td>
                            <textarea id="email_message" name="message" rows="10" cols="50" class="large-text" required><?php echo wp_kses_post($this->get_template_for_email()); ?></textarea>
                        </td>
                    </tr>
                </table>

                <div id="customer-preview"></div>

                <p class="submit">
                    <input type="button" id="preview-customers" class="button" value="Preview Recipients">
                    <input type="submit" class="button-primary" value="Send Emails">
                </p>
            </form>

            <div id="email-results"></div>
        </div>
        <?php
    }

    /**
     * Method get_template_title_email
     *
     * @return string
     */
    public function get_template_title_email()
    {
        ob_start();
        ?>Dogma - Meet & Greet - <?php
                                    $content = ob_get_clean();
                                    return $content;
                                }

                                /**
                                 * Method get_template_for_email
                                 *
                                 * @return string
                                 */
                                public function get_template_for_email()
                                {
                                    ob_start();
                                    ?>
        <p>Dear Sinner,</p>
        <p>Your devotion has been recognized. You hold a key that few possess: a pass to meet DOGMA beyond the stage, where the real rituals begin.</p>
        <p>Here are the details of your meeting with us:</p>
        <ul>
            <li>
                <p><strong>Date:</strong>&nbsp;FECHA</p>
            </li>
            <li>
                <p><strong>City:</strong>&nbsp;CIUDAD</p>
            </li>
            <li>
                <p><strong>Venue:</strong>&nbsp;VENUE</p>
            </li>
            <li>
                <p><strong>Meet &amp; Greet Time:</strong>&nbsp;[Insert Exact Time]<br /><em>Please arrive&nbsp;<strong>15 minutes before</strong>&nbsp;the scheduled time to ensure you do not miss the experience.</em></p>
            </li>
        </ul>
        <p>During the Meet &amp; Greet, you will have the chance to:</p>
        <ul>
            <li>
                <p>Meet DOGMA in person.</p>
            </li>
            <li>
                <p>Take an official photo.</p>
            </li>
            <li>
                <p>Get limited items signed (up to two per person).</p>
            </li>
            <li>
                <p>Witness the fire behind the performance.</p>
            </li>
        </ul>
        <p><strong>Important:</strong></p>
        <p><strong>DOGMA reserves the right of admission.</strong></p>
        <div>If you arrive&nbsp;<strong>visibly intoxicated</strong>&nbsp;or&nbsp;<strong>under the influence of any substance</strong>, you will be&nbsp;<strong>denied access</strong>&nbsp;to the meet &amp; greet.&nbsp;<strong>No exceptions. No refunds.</strong></div>
        <div>You enter our world by choice.</div>
        <div><br />This gathering is not a negotiation. It is an act of mutual recognition. Come ready. Come as you are or as you wish to become.</div>
        <p>The stage is just the beginning. The true DOGMA is whispered in the moments before and after the fire.</p>
        <p>We embrace you sinner,</p>
        <p>DOGMA</p>
<?php
                                    $content = ob_get_clean();
                                    return $content;
                                }
                            }
