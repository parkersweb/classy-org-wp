<?php

class ClassyOrg_CampaignOverviewWidget extends WP_Widget
{
    const ID = 'ClassyOrg_CampaignOverviewWidget';

    /**
     * Create instance of widget
     */
    public function __construct()
    {
        parent::__construct(self::ID, 'Classy.org: Campaign Overview');
    }

    /**
     * Draw form for widget options.
     *
     * @param array $instance
     * @return null
     */
    public function form($instance)
    {
        if ($instance) {
            $title = array_key_exists('title', $instance) ? $instance['title'] : '';
            $campaignId = array_key_exists('id', $instance) ? $instance['id'] : '';
        } else {
            $title = '';
            $campaignId = '';
        }

        echo '<div class="widget-content">';

        // Campaign ID
        echo '<p>'
            . '<label for="' . $this->get_field_name('id') . '">' . _e('Campaign ID:') . '</label>'
            . '<input class="widefat" id="' . $this->get_field_id('id')
            . '" name="' . $this->get_field_name('id') . '" type="text" value="'
            . esc_attr($campaignId) . '" placeholder="123456789" />'
            . '</p>';

        // Title
        echo '<p>'
            . '<label for="' . $this->get_field_name('title') . '">' . _e('Title:') . '</label>'
            . '<input class="widefat" id="' . $this->get_field_id('title')
                . '" name="' . $this->get_field_name('title') . '" type="text" value="'
                . esc_attr($title) . '" placeholder="My Campaign Title" />'
            . '</p>';

        echo '</div>';

    }

    /**
     * Update settings
     *
     * @param array $newInstance
     * @param array $oldInstance
     * @return array
     */
    public function update($newInstance, $oldInstance)
    {
        $instance = $oldInstance;

        // FIXME: validate parameters
        $instance['id'] = strip_tags($newInstance['id']);
        $instance['title'] = strip_tags($newInstance['title']);

        return $instance;
    }

    /**
     * Draw widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        // Defer to renderer which we also use for short codes
        $classyContent = new ClassyContent();
        $campaign = $classyContent->campaignOverview($instance['id']);

        ClassyOrg::addStylesheet();
        echo self::render($campaign, $instance);
    }

    /**
     * Generate HTML for campaign progress
     *
     * @param $campaign
     * @param $params
     * @return string
     */
    public static function render($campaign, $params)
    {
        $widgetTemplate = <<<WIDGET_TEMPLATE

        <div class="classy-org-overview classy-org-widget widget">
          %s
          <div class="classy-org-overview_item">
            <span class="classy-org-overview_item-stat">%s</span>
            <span class="classy-org-overview_item-label">Gross Transactions</span>
          </div>
          <div class="classy-org-overview_item">
            <span class="classy-org-overview_item-stat">%s</span>
            <span class="classy-org-overview_item-label">Donors</span>
          </div>
          <div class="classy-org-overview_item">
            <span class="classy-org-overview_item-stat">%s</span>
            <span class="classy-org-overview_item-label">Transactions</span>
          </div>
          <div class="classy-org-overview_item">
            <span class="classy-org-overview_item-stat">%s</span>
            <span class="classy-org-overview_item-label">Average Transaction</span>
          </div>
        </div>
        <div style="clear: both;"></div>

WIDGET_TEMPLATE;

        $title = (!empty($params['title']))
            ? sprintf('<h3 class="classy-org-overview_title">%s</h3>', $params['title'])
            : '';

        $grossTransactions = ceil($campaign['overview']['total_gross_amount']);
        $donorCount = (int)$campaign['overview']['donors_count'];
        $transactionCount = (int)$campaign['overview']['transactions_count'];
        $averageTransaction = round($campaign['overview']['total_gross_amount'] / $transactionCount, 2);

        $html = sprintf(
            $widgetTemplate,
            $title,
            $grossTransactions,
            $donorCount,
            $transactionCount,
            $averageTransaction
        );

        return $html;
    }
}