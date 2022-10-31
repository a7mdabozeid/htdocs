<?php

namespace WPML\Core;

use \WPML\Core\Twig\Environment;
use \WPML\Core\Twig\Error\LoaderError;
use \WPML\Core\Twig\Error\RuntimeError;
use \WPML\Core\Twig\Markup;
use \WPML\Core\Twig\Sandbox\SecurityError;
use \WPML\Core\Twig\Sandbox\SecurityNotAllowedTagError;
use \WPML\Core\Twig\Sandbox\SecurityNotAllowedFilterError;
use \WPML\Core\Twig\Sandbox\SecurityNotAllowedFunctionError;
use \WPML\Core\Twig\Source;
use \WPML\Core\Twig\Template;

/* st-taxonomy-ui.twig */
class __TwigTemplate_59b78d2450560f76de04d4ca6134cf1b7fb220e67f03d77250e5655d23ab17a6 extends \WPML\Core\Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<p>
    <a href=\"";
        // line 2
        echo \WPML\Core\twig_escape_filter($this->env, ($context["link_url"] ?? null), "html", null, true);
        echo "\">
        ";
        // line 3
        echo \WPML\Core\twig_escape_filter($this->env, ($context["link_label"] ?? null), "html", null, true);
        echo "
    </a>
</p>
";
    }

    public function getTemplateName()
    {
        return "st-taxonomy-ui.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  39 => 3,  35 => 2,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<p>
    <a href=\"{{ link_url }}\">
        {{ link_label }}
    </a>
</p>
", "st-taxonomy-ui.twig", "/home/alshia6/public_html/wp-content/plugins/woocommerce-multilingual/templates/st-taxonomy-ui.twig");
    }
}
