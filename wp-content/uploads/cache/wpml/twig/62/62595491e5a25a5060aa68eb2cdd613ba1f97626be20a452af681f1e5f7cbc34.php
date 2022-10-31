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

/* edit-base.twig */
class __TwigTemplate_22093bd6adbc361bba7bf7fe5ec8f1658cca9072fc58f4dcad688368dde085de extends \WPML\Core\Twig\Template
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
        echo "<div class=\"wcml-dialog hidden\" id=\"wcml-edit-base-slug-";
        echo \WPML\Core\twig_escape_filter($this->env, ($context["original_base"] ?? null));
        echo "-";
        echo \WPML\Core\twig_escape_filter($this->env, ($context["language"] ?? null));
        echo "\" title=\"";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "label_name", []), "html", null, true);
        echo "\">
    <form class=\"wcml-slug-dialog\" >

        <h3 class=\"wpml-header-original\">";
        // line 4
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "orig", []), "html", null, true);
        echo ":
\t\t\t\t<span class=\"wpml-title-flag\">
\t\t\t\t\t<img src=\"";
        // line 6
        echo \WPML\Core\twig_escape_filter($this->env, ($context["orig_flag_url"] ?? null), "html", null, true);
        echo "\" alt=\"";
        echo \WPML\Core\twig_escape_filter($this->env, ($context["orig_display_name"] ?? null));
        echo "\"/>
\t\t\t\t</span>
            <strong>";
        // line 8
        echo \WPML\Core\twig_escape_filter($this->env, ($context["orig_display_name"] ?? null), "html", null, true);
        echo "</strong>
        </h3>

        <h3 class=\"wpml-header-translation\">";
        // line 11
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "trnsl_to", []), "html", null, true);
        echo ":
\t\t\t\t<span class=\"wpml-title-flag\">
\t\t\t\t\t<img src=\"";
        // line 13
        echo \WPML\Core\twig_escape_filter($this->env, ($context["trnsl_flag_url"] ?? null), "html", null, true);
        echo "\" alt=\"";
        echo \WPML\Core\twig_escape_filter($this->env, ($context["trnsl_display_name"] ?? null));
        echo "\"/>
\t\t\t\t</span>
            <strong>";
        // line 15
        echo \WPML\Core\twig_escape_filter($this->env, ($context["trnsl_display_name"] ?? null), "html", null, true);
        echo "</strong>
        </h3>

        <div class=\"wpml-form-row\">
            <input readonly id=\"base-original\" class=\"original_value\" value=\"";
        // line 19
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "original_base_value", []), "html", null, true);
        echo "\"
                   type=\"text\">

            <input id=\"base-translation\"
                   class=\"translated_value\"
                   name=\"base_translation\" value=\"";
        // line 24
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "translated_base_value", []), "html", null, true);
        echo "\" type=\"text\"/>
        </div>

        <footer class=\"wpml-dialog-footer\">
            <input type=\"button\" class=\"cancel wcml-dialog-close-button wpml-dialog-close-button wcml_cancel_base alignleft\"
                   value=\"";
        // line 29
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "cancel", []));
        echo "\" />
            <input type=\"submit\" class=\"wcml-dialog-close-button wpml-dialog-close-button button-primary wcml_save_base alignright\"
                   value=\"";
        // line 31
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "save", []));
        echo "\" data-base=\"";
        echo \WPML\Core\twig_escape_filter($this->env, ($context["original_base"] ?? null));
        echo "\" data-language=\"";
        echo \WPML\Core\twig_escape_filter($this->env, ($context["language"] ?? null));
        echo "\"/>
        </footer>
    </form>
</div>";
    }

    public function getTemplateName()
    {
        return "edit-base.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  101 => 31,  96 => 29,  88 => 24,  80 => 19,  73 => 15,  66 => 13,  61 => 11,  55 => 8,  48 => 6,  43 => 4,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"wcml-dialog hidden\" id=\"wcml-edit-base-slug-{{ original_base|e }}-{{ language|e }}\" title=\"{{ data.label_name }}\">
    <form class=\"wcml-slug-dialog\" >

        <h3 class=\"wpml-header-original\">{{ strings.orig }}:
\t\t\t\t<span class=\"wpml-title-flag\">
\t\t\t\t\t<img src=\"{{ orig_flag_url }}\" alt=\"{{ orig_display_name|e }}\"/>
\t\t\t\t</span>
            <strong>{{ orig_display_name }}</strong>
        </h3>

        <h3 class=\"wpml-header-translation\">{{ strings.trnsl_to }}:
\t\t\t\t<span class=\"wpml-title-flag\">
\t\t\t\t\t<img src=\"{{ trnsl_flag_url }}\" alt=\"{{ trnsl_display_name|e }}\"/>
\t\t\t\t</span>
            <strong>{{ trnsl_display_name }}</strong>
        </h3>

        <div class=\"wpml-form-row\">
            <input readonly id=\"base-original\" class=\"original_value\" value=\"{{ data.original_base_value }}\"
                   type=\"text\">

            <input id=\"base-translation\"
                   class=\"translated_value\"
                   name=\"base_translation\" value=\"{{ data.translated_base_value }}\" type=\"text\"/>
        </div>

        <footer class=\"wpml-dialog-footer\">
            <input type=\"button\" class=\"cancel wcml-dialog-close-button wpml-dialog-close-button wcml_cancel_base alignleft\"
                   value=\"{{ strings.cancel|e }}\" />
            <input type=\"submit\" class=\"wcml-dialog-close-button wpml-dialog-close-button button-primary wcml_save_base alignright\"
                   value=\"{{ strings.save|e }}\" data-base=\"{{ original_base|e }}\" data-language=\"{{ language|e }}\"/>
        </footer>
    </form>
</div>", "edit-base.twig", "/home/alshia5/public_html/wp-content/plugins/woocommerce-multilingual/templates/store-urls/edit-base.twig");
    }
}
