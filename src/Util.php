<?php
namespace Eduardokum\LaravelBoleto;

final class Util
{

    /**
     * Retorna a String em MAIUSCULO
     *
     * @param String $string
     *
     * @return String
     */
    public static function upper($string)
    {
        return strtr(strtoupper($string), "àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ", "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß");
    }

    /**
     * Retorna a String em minusculo
     *
     * @param String $string
     *
     * @return String
     */
    public static function lower($string)
    {
        return strtr(strtolower($string), "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß", "àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ");
    }

    /**
     * Retorna a String em minusculo
     *
     * @param String $string
     *
     * @return String
     */
    public static function upFirst($string)
    {
        return ucfirst(self::lower($string));
    }

    /**
     * Retorna somente as letras da string
     *
     * @param String $string
     *
     * @return String
     */
    public static function lettersOnly($string)
    {
        return preg_replace('/[^[:alpha:]]/', '', $string);
    }

    /**
     * Retorna somente as letras da string
     *
     * @param String $string
     *
     * @return String
     */
    public static function onlyLetters($string)
    {
        return self::lettersOnly($string);
    }

    /**
     * Retorna somente as letras da string
     *
     * @param String $string
     *
     * @return String
     */
    public static function lettersNot($string)
    {
        return preg_replace('/[[:alpha:]]/', '', $string);
    }

    /**
     * Retorna somente as letras da string
     *
     * @param String $string
     *
     * @return String
     */
    public static function notLetters($string)
    {
        return self::lettersNot($string);
    }

    /**
     * Retorna somente os digitos da string
     *
     * @param String $string
     *
     * @return String
     */
    public static function numbersOnly($string)
    {
        return preg_replace('/[^[:digit:]]/', '', $string);
    }

    /**
     * Retorna somente os digitos da string
     *
     * @param String $string
     *
     * @return String
     */
    public static function onlyNumbers($string)
    {
        return self::numbersOnly($string);
    }

    /**
     * Retorna somente os digitos da string
     *
     * @param String $string
     *
     * @return String
     */
    public static function numbersNot($string)
    {
        return preg_replace('/[[:digit:]]/', '', $string);
    }

    /**
     * Retorna somente os digitos da string
     *
     * @param String $string
     *
     * @return String
     */
    public static function notNumbers($string)
    {
        return self::numbersNot($string);
    }

    /**
     * Retorna somente alfanumericos
     *
     * @param String $string
     *
     * @return String
     */
    public static function alphanumberOnly($string)
    {
        return preg_replace('/[^[:alnum:]]/', '', $string);
    }

    /**
     * Retorna somente alfanumericos
     *
     * @param String $string
     *
     * @return String
     */
    public static function onlyAlphanumber($string)
    {
        return self::alphanumberOnly($string);
    }

    /**
     * Função para limpar acentos de uma string
     *
     * @param string $string
     * @return string
     */
    public static function normalizeChars($string) {

        $normalizeChars = array(
            'Á'=>'A', 'À'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Å'=>'A', 'Ä'=>'A', 'Æ'=>'AE', 'Ç'=>'C',
            'É'=>'E', 'È'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Í'=>'I', 'Ì'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ð'=>'Eth',
            'Ñ'=>'N', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',
            'Ú'=>'U', 'Ù'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Ŕ'=>'R',

            'á'=>'a', 'à'=>'a', 'â'=>'a', 'ã'=>'a', 'å'=>'a', 'ä'=>'a', 'æ'=>'ae', 'ç'=>'c',
            'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e', 'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'eth',
            'ñ'=>'n', 'ó'=>'o', 'ò'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o',
            'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ŕ'=>'r', 'ÿ'=>'y',

            'ß'=>'sz', 'þ'=>'thorn'
        );
        return strtr($string,$normalizeChars);
    }

    /**
     * Mostra o Valor no float Formatado
     * @param float $number
     * @param integer $decimals
     * @param boolean $showThousands
     * @return string
     */
    public static function nFloat($number, $decimals = 2, $showThousands = true)
    {
        if(is_null($number) || !is_numeric($number)) {return '';}
        $pontuacao = preg_replace('/[0-9]/', '', $number);
        $locale = (substr($pontuacao, -1, 1) == ',')?"pt-BR":"en-US";
        $formater = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);

        if( $decimals === false )
        {
            $decimals = 2;
            preg_match_all('/[0-9][^0-9]([0-9]+)/', $number, $matches);
            if( !empty($matches[1]) )
            {
                $decimals = strlen(rtrim($matches[1][0], 0));
            }
        }

        return number_format($formater->parse( $number, \NumberFormatter::TYPE_DOUBLE), $decimals, '.', ($showThousands)?',':'');
    }

    /**
     * Mostra o Valor no real Formatado
     * @param float $number
     * @param boolean $fixed
     * @param boolean $symbol
     * @param integer $decimals
     * @return string
     */
    public static function nReal($number, $decimals = 2, $symbol = true, $fixed = true)
    {
        if(is_null($number) || !is_numeric($number)) {return '';}
        $formater = new \NumberFormatter("pt-BR", \NumberFormatter::CURRENCY);
        $formater->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, ($fixed?$decimals:1));
        if( $decimals === false )
        {
            $decimals = 2;
            preg_match_all('/[0-9][^0-9]([0-9]+)/', $number, $matches);
            if( !empty($matches[1]) )
            {
                $decimals = strlen(rtrim($matches[1][0], 0));
            }
        }
        $formater->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
        if(!$symbol) {
            $pattern = preg_replace("/[¤]/", '', $formater->getPattern());
            $formater->setPattern($pattern);
        } else {
            // ESPAÇO DEPOIS DO SIMBOLO
            $pattern = str_replace("¤", "¤ ", $formater->getPattern());
            $formater->setPattern($pattern);
        }
        return $formater->formatCurrency($number, $formater->getTextAttribute(\NumberFormatter::CURRENCY_CODE));
    }

    /**
     * Mostra um numero por extenso.
     *
     * @param $value
     *
     * @param $uppercase 1 - UPPER; 2 - Upper; false - tudo minusculo;
     * @return string
     */
    public static function nRealExtenso($value, $uppercase)
    {
        $value = self::nFloat($value, 2);

        $singular = ["centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"];
        $plural = ["centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões"];

        $c = ["", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"];
        $d = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
        $d10 = ["dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove"];
        $u = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];

        $z = 0;

        $value = number_format($value, 2, ".", ".");
        $integer = explode(".", $value);
        $cont = count($integer);
        for ($i = 0; $i < $cont; $i++)
            for ($ii = strlen($integer[$i]); $ii < 3; $ii++)
                $integer[$i] = "0" . $integer[$i];

        $fim = $cont - ($integer[$cont - 1] > 0 ? 1 : 2);
        $rt = '';
        for ($i = 0; $i < $cont; $i++) {
            $value = $integer[$i];
            $rc = (($value > 100) && ($value < 200)) ? "cento" : $c[$value[0]];
            $rd = ($value[1] < 2) ? "" : $d[$value[1]];
            $ru = ($value > 0) ? (($value[1] == 1) ? $d10[$value[2]] : $u[$value[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
                    $ru) ? " e " : "") . $ru;
            $t = $cont - 1 - $i;
            $r .= $r ? " " . ($value > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($value == "000"
            )
                $z++;
            elseif ($z > 0)
                $z--;
            if (($t == 1) && ($z > 0) && ($integer[0] > 0))
                $r .= ( ($z > 1) ? " de " : "") . $plural[$t];
            if ($r)
                $rt = $rt . ((($i > 0) && ($i <= $fim) &&
                        ($integer[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        if (!$uppercase) {
            return trim($rt ? $rt : "zero");
        } elseif ($uppercase == "2") {
            return trim(strtoupper($rt) ? strtoupper(strtoupper($rt)) : "Zero");
        } else {
            return trim(ucwords($rt) ? ucwords($rt) : "Zero");
        }
    }

    /**
     * Return percent x of y;
     *
     * @param     $big
     * @param     $small
     * @param int $defaultOnZero
     *
     * @return string
     */
    public static function percentOf($big, $small, $defaultOnZero = 0)
    {
        $result = $big > 0.01 ? (($small*100)/$big) : $defaultOnZero;
        return self::nFloat($result);
    }

    /**
     * Função para mascarar uma string, mascara tipo ##-##-##
     *
     * @param string $val
     * @param string $mask
     *
     * @return string
     */
    public static function maskString($val, $mask)
    {
        if(empty($val))
        {
            return $val;
        }
        $maskared = '';
        $k = 0;
        if (is_numeric($val))
        {
            $val = sprintf('%0' . strlen(preg_replace('/[^#]/', '', $mask)) . 's', $val);
        }
        for ($i = 0; $i <= strlen($mask) - 1; $i ++)
        {
            if ($mask[$i] == '#')
            {
                if (isset($val[$k]))
                {
                    $maskared .= $val[$k ++];
                }
            } else
            {
                if (isset($mask[$i]))
                {
                    $maskared .= $mask[$i];
                }
            }
        }

        return $maskared;
    }

    /**
     *
     * @param string $date
     *
     * @return string
     */
    public static function dateBrToUs($date)
    {
        if (empty($date))
        {
            return false;
        }
        $numbersOnly = self::numbersOnly($date);
        if (self::maskString($numbersOnly, '##/##/####') == $date)
        {
            $date_time = \DateTime::createFromFormat('d/m/Y', $date);
        } elseif (self::maskString($numbersOnly, '##/##/##') == $date)
        {
            $date_time = \DateTime::createFromFormat('d/m/y', $date);
        } else
        {
            return false;
        }

        return $date_time->format('Y-m-d');
    }

}