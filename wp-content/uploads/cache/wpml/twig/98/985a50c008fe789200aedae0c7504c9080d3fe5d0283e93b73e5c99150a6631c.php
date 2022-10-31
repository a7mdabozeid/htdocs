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

/* status.twig */
class __TwigTemplate_08c7c9c34ed68a0ac92a3b8f1d68ff44e69952a7894a9a6b8eecb79ea31ad543 extends \WPML\Core\Twig\Template
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
        echo ($context["conf_warnings"] ?? null);
        echo "

";
        // line 3
        echo ($context["store_pages"] ?? null);
        echo "

";
        // line 5
        echo ($context["products"] ?? null);
        echo "

";
        // line 7
        echo ($context["taxonomies"] ?? null);
        echo "

";
        // line 9
        echo ($context["multi_currency"] ?? null);
        echo "

";
        // line 11
        echo ($context["plugins_status"] ?? null);
        echo "

";
        // line 13
        echo ($context["media"] ?? null);
        echo "

<a class=\"alignright wpml-margin-top-sm\" href=\"";
        // line 15
        echo \WPML\Core\twig_escape_filter($this->env, ($context["troubl_url"] ?? null), "html", null, true);
        echo "\">
    ";
        // line 16
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "troubl", []), "html", null, true);
        echo "
</a>";
    }

    public function getTemplateName()
    {
        return "status.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 16,  67 => 15,  62 => 13,  57 => 11,  52 => 9,  47 => 7,  42 => 5,  37 => 3,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{{ conf_warnings|raw }}

{{ store_pages|raw }}

{{ products|raw }}

{{ taxonomies|raw }}

{{ multi_currency|raw }}

{{ plugins_status|raw }}

{{ media|raw }}

<a class=\"alignright wpml-margin-top-sm\" href=\"{{ troubl_url }}\">
    {{ strings.troubl }}
</a>", "status.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/status/status.twig");
    }
}
