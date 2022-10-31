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

/* trnsl-attributes.twig */
class __TwigTemplate_eaa0daa127edaedeb41d16b6be361dc0dac27035606db89dd1dbb1b3bef89b9c extends \WPML\Core\Twig\Template
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
        if (($context["edit_mode"] ?? null)) {
            // line 2
            echo "    <div class=\"wcml-is-translatable-attr-block\" style=\"display: none\">
        <table>
            <tr class=\"form-field\">
                <th scope=\"row\" valign=\"top\">
                    <label for=\"wcml-is-translatable-attr\">";
            // line 6
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "label", []), "html", null, true);
            echo "</label>
                </th>
                <td>
                    <input name=\"wcml-is-translatable-attr\" id=\"wcml-is-translatable-attr\" type=\"checkbox\" value=\"1\" ";
            // line 9
            if (($context["checked"] ?? null)) {
                echo " checked=\"checked\" ";
            }
            echo " />
                    <p class=\"description\">";
            // line 10
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "description", []), "html", null, true);
            echo "</p>
                </td>
            </tr>
        </table>
    </div>
    <input type=\"hidden\" id=\"wcml-is-translatable-attr-notice\" value=\"";
            // line 15
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "notice", []), "html", null, true);
            echo "\" />
";
        } else {
            // line 17
            echo "    <div class=\"wcml-is-translatable-attr-block\" style=\"display: none\">
        <div class=\"form-field\">
            <label for=\"wcml-is-translatable-attr\">
                <input name=\"wcml-is-translatable-attr\" id=\"wcml-is-translatable-attr\" type=\"checkbox\" value=\"1\" ";
            // line 20
            if (($context["checked"] ?? null)) {
                echo " checked=\"checked\" ";
            }
            echo " />
                ";
            // line 21
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "label", []), "html", null, true);
            echo "
            </label>
            <p class=\"description\">";
            // line 23
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "description", []), "html", null, true);
            echo "</p>
        </div>
    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "trnsl-attributes.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  81 => 23,  76 => 21,  70 => 20,  65 => 17,  60 => 15,  52 => 10,  46 => 9,  40 => 6,  34 => 2,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{% if edit_mode %}
    <div class=\"wcml-is-translatable-attr-block\" style=\"display: none\">
        <table>
            <tr class=\"form-field\">
                <th scope=\"row\" valign=\"top\">
                    <label for=\"wcml-is-translatable-attr\">{{ strings.label }}</label>
                </th>
                <td>
                    <input name=\"wcml-is-translatable-attr\" id=\"wcml-is-translatable-attr\" type=\"checkbox\" value=\"1\" {% if checked %} checked=\"checked\" {% endif %} />
                    <p class=\"description\">{{ strings.description }}</p>
                </td>
            </tr>
        </table>
    </div>
    <input type=\"hidden\" id=\"wcml-is-translatable-attr-notice\" value=\"{{ strings.notice }}\" />
{% else %}
    <div class=\"wcml-is-translatable-attr-block\" style=\"display: none\">
        <div class=\"form-field\">
            <label for=\"wcml-is-translatable-attr\">
                <input name=\"wcml-is-translatable-attr\" id=\"wcml-is-translatable-attr\" type=\"checkbox\" value=\"1\" {% if checked %} checked=\"checked\" {% endif %} />
                {{ strings.label }}
            </label>
            <p class=\"description\">{{ strings.description }}</p>
        </div>
    </div>
{% endif %}", "trnsl-attributes.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/trnsl-attributes.twig");
    }
}
