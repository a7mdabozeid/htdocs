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

/* template.twig */
class __TwigTemplate_563642097419750ca1e4ed864f4a1aab8f4e0c393dd8b02032d43cb71ecfc68e extends \WPML\Core\Twig\Template
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
        echo "<div class=\"";
        echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes"] ?? null), "html", null, true);
        echo "\" >
\t<ul>
\t\t<li class=\"wcml-cs-active-currency\" >
\t\t\t<a class=\"wcml-cs-item-toggle\">";
        // line 4
        echo call_user_func_array($this->env->getFunction('get_formatted_price')->getCallable(), [($context["selected_currency"] ?? null), ($context["format"] ?? null)]);
        echo "</a>
\t\t\t<ul class=\"wcml-cs-submenu\">
\t\t\t\t";
        // line 6
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["currencies"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["currency"]) {
            // line 7
            echo "\t\t\t\t\t";
            if (($context["currency"] != ($context["selected_currency"] ?? null))) {
                // line 8
                echo "\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t<a rel=\"";
                // line 9
                echo \WPML\Core\twig_escape_filter($this->env, $context["currency"], "html", null, true);
                echo "\">";
                echo call_user_func_array($this->env->getFunction('get_formatted_price')->getCallable(), [$context["currency"], ($context["format"] ?? null)]);
                echo "</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t";
            }
            // line 12
            echo "\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['currency'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        echo "\t\t\t</ul>
\t\t</li>
\t</ul>
</div>";
    }

    public function getTemplateName()
    {
        return "template.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 13,  62 => 12,  54 => 9,  51 => 8,  48 => 7,  44 => 6,  39 => 4,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"{{ css_classes }}\" >
\t<ul>
\t\t<li class=\"wcml-cs-active-currency\" >
\t\t\t<a class=\"wcml-cs-item-toggle\">{{ get_formatted_price( selected_currency, format )|raw }}</a>
\t\t\t<ul class=\"wcml-cs-submenu\">
\t\t\t\t{% for currency in currencies %}
\t\t\t\t\t{% if currency != selected_currency %}
\t\t\t\t\t\t<li>
\t\t\t\t\t\t\t<a rel=\"{{ currency }}\">{{ get_formatted_price( currency, format )|raw }}</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t{% endif %}
\t\t\t\t{% endfor %}
\t\t\t</ul>
\t\t</li>
\t</ul>
</div>", "template.twig", "/home/alshia6/public_html/wp-content/plugins/woocommerce-multilingual/templates/currency-switchers/legacy-dropdown/template.twig");
    }
}
