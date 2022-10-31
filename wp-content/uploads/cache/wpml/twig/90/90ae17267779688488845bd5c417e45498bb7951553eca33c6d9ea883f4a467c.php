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

/* products.twig */
class __TwigTemplate_1ef2c9c29d257f3a566d99e22dad01fd1cec6e6f5cd73180cdea8551fabc8c02 extends \WPML\Core\Twig\Template
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
        echo "<div class=\"wcml-section wc-products-section\">
    <div class=\"wcml-section-header\">
        <h3>
            ";
        // line 4
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "products_missing", []), "html", null, true);
        echo "
        </h3>
    </div>
    <div class=\"wcml-section-content\">
        <ul class=\"wcml-status-list wcml-plugins-status-list\">
            ";
        // line 9
        if (($context["auto_trnsl_products"] ?? null)) {
            // line 10
            echo "                <div class=\"notice wpml-notice inline\">";
            echo $this->getAttribute(($context["strings"] ?? null), "auto_trnsl_prod", []);
            echo "</div>
            ";
        } elseif (twig_test_empty(        // line 11
($context["products"] ?? null))) {
            // line 12
            echo "                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    ";
            // line 14
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "not_to_trnsl", []), "html", null, true);
            echo "
                </li>
            ";
        } else {
            // line 17
            echo "                ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["products"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
                // line 18
                echo "                    <li>
                        <i class=\"otgs-ico-warning\"></i>
                        <span class=\"wpml-title-flag\">
                            ";
                // line 21
                echo $this->getAttribute($context["product"], "flag", []);
                echo "
                        </span>
                        ";
                // line 23
                if (($this->getAttribute($context["product"], "count", []) == 1)) {
                    // line 24
                    echo "                            ";
                    echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "miss_trnsl_one", []), $this->getAttribute($context["product"], "count", []), $this->getAttribute($context["product"], "display_name", [])), "html", null, true);
                    echo "
                        ";
                } else {
                    // line 26
                    echo "                            ";
                    echo \WPML\Core\twig_escape_filter($this->env, sprintf($this->getAttribute(($context["strings"] ?? null), "miss_trnsl_more", []), $this->getAttribute($context["product"], "count", []), $this->getAttribute($context["product"], "display_name", [])), "html", null, true);
                    echo "
                        ";
                }
                // line 28
                echo "                    </li>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 30
            echo "
                <p>
                    <a class=\"button-secondary aligncenter\" href=\"";
            // line 32
            echo \WPML\Core\twig_escape_filter($this->env, ($context["trnsl_link"] ?? null), "html", null, true);
            echo "\">
                        ";
            // line 33
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "transl", []), "html", null, true);
            echo "
                    </a>
                </p>
            ";
        }
        // line 37
        echo "        </ul>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "products.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  115 => 37,  108 => 33,  104 => 32,  100 => 30,  93 => 28,  87 => 26,  81 => 24,  79 => 23,  74 => 21,  69 => 18,  64 => 17,  58 => 14,  54 => 12,  52 => 11,  47 => 10,  45 => 9,  37 => 4,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"wcml-section wc-products-section\">
    <div class=\"wcml-section-header\">
        <h3>
            {{ strings.products_missing }}
        </h3>
    </div>
    <div class=\"wcml-section-content\">
        <ul class=\"wcml-status-list wcml-plugins-status-list\">
            {% if auto_trnsl_products %}
                <div class=\"notice wpml-notice inline\">{{ strings.auto_trnsl_prod|raw }}</div>
            {% elseif products is empty %}
                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    {{ strings.not_to_trnsl }}
                </li>
            {% else %}
                {% for product in products %}
                    <li>
                        <i class=\"otgs-ico-warning\"></i>
                        <span class=\"wpml-title-flag\">
                            {{ product.flag|raw }}
                        </span>
                        {% if(product.count == 1) %}
                            {{ strings.miss_trnsl_one|format( product.count, product.display_name ) }}
                        {% else %}
                            {{ strings.miss_trnsl_more|format( product.count, product.display_name ) }}
                        {% endif %}
                    </li>
                {% endfor %}

                <p>
                    <a class=\"button-secondary aligncenter\" href=\"{{ trnsl_link }}\">
                        {{ strings.transl }}
                    </a>
                </p>
            {% endif %}
        </ul>
    </div>
</div>", "products.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/status/products.twig");
    }
}
