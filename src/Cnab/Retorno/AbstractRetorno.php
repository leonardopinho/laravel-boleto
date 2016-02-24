<?php
/**
 *   Copyright (c) 2016 Eduardo Gusmão
 *
 *   Permission is hereby granted, free of charge, to any person obtaining a
 *   copy of this software and associated documentation files (the "Software"),
 *   to deal in the Software without restriction, including without limitation
 *   the rights to use, copy, modify, merge, publish, distribute, sublicense,
 *   and/or sell copies of the Software, and to permit persons to whom the
 *   Software is furnished to do so, subject to the following conditions:
 *
 *   The above copyright notice and this permission notice shall be included in all
 *   copies or substantial portions of the Software.
 *
 *   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 *   INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 *   PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *   COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 *   WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
 *   IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Eduardokum\LaravelBoleto\Cnab\Retorno;

use Eduardokum\LaravelBoleto\Util;
use Eduardokum\LaravelBoleto\Contracts\Cnab\Retorno\Header as HeaderContract;
use Eduardokum\LaravelBoleto\Contracts\Cnab\Retorno\Detalhe as DetalheContract;
use Eduardokum\LaravelBoleto\Contracts\Cnab\Retorno\Trailer as TrailerContract;

abstract class AbstractRetorno implements \Countable, \SeekableIterator
{

    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco;

    /**
     * Incremeto de detalhes
     *
     * @var int
     */
    private $increment = 0;

    /**
     * Arquivo transformado em array por linha.
     *
     * @var array
     */
    protected $file;

    /**
     * @var int
     */
    private $_position = 0;

    /**
     * @var HeaderContract
     */
    private $header;

    /**
     * @var TrailerContract
     */
    private $trailer;

    /**
     * @var DetalheContract[]
     */
    private $detalhe = [];

    /**
     * Helper de totais.
     *
     * @var array
     */
    protected $totais = [];

    /**
     *
     * @param String $file
     * @throws \Exception
     */
    public function __construct($file) {

        $this->_position = 0;
        if(is_array($file) && strlen(rtrim($file[0], chr(10).chr(13)."\n"."\r")) == 400)
        {
            $this->file = $file;
        }
        else if(is_file($file) && file_exists($file))
        {
            $this->file = file($file);
        }
        else if(is_string($file)) {
            $this->file = preg_split('/\r\n|\r|\n/', $file);
            if(empty(end($this->file)))
            {
                array_pop($this->file);
            }
            reset($this->file);
        }
        else
        {
            throw new \Exception("Arquivo: não existe");
        }

        $r = new \ReflectionClass('\Eduardokum\LaravelBoleto\Contracts\Cnab\Cnab');
        $constantNames = $r->getConstants();
        $bancosDisponiveis = [];
        foreach($constantNames as $constantName => $codigoBanco)
        {
            if(preg_match('/^COD_BANCO.*/', $constantName))
            {
                $bancosDisponiveis[] = $codigoBanco;
            }
        }

        if(substr($this->file[0], 0, 9) != '02RETORNO')
        {
            throw new \Exception(sprintf("Arquivo de retorno inválido"));
        }

        if(!in_array(substr($this->file[0], 76, 3), $bancosDisponiveis))
        {
            throw new \Exception(sprintf("Banco: %s, inválido", substr($this->file[0], 76, 3)));
        }

        $this->header = new Header();
        $this->trailer = new Trailer();
    }

    /**
     * Retorna o código do banco
     *
     * @return string
     */
    public function getCodigoBanco()
    {
        return $this->codigoBanco;
    }

    /**
     * @return mixed
     */
    public function getBancoNome() {
        return Util::$bancos[$this->banco];
    }

    /**
     * @return Detalhe[]
     */
    public function getDetalhes() {
        return $this->detalhe;
    }

    /**
     * @param $i
     *
     * @return Detalhe
     */
    public function getDetalhe($i) {
        return array_key_exists($i, $this->detalhe) ? $this->detalhe[$i] : null;
    }

    /**
     * @return Header
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * @return Trailer
     */
    public function getTrailer() {
        return $this->trailer;
    }

    /**
     * Incrementa o detalhe.
     */
    protected function incrementDetalhe()
    {
        $this->increment ++;
        $this->detalhe[$this->increment] = new Detalhe();
    }

    /**
     * Retorna o detalhe atual.
     *
     * @return Detalhe
     */
    protected function detalheAtual()
    {
        return $this->detalhe[$this->increment];
    }

    /**
     * @param array $header
     *
     * @return boolean
     */
    protected abstract function processarHeader(array $header);

    /**
     * @param array $detalhe
     *
     * @return boolean
     */
    protected abstract function processarDetalhe(array $detalhe);

    /**
     * @param array $trailer
     *
     * @return boolean
     */
    protected abstract function processarTrailer(array $trailer);

    /**
     * Processa o arquivo
     */
    public function processar() {

        if(method_exists($this, 'init')) {
            $this->init();
        }
        foreach($this->file as $linha) {
            $aLinha = str_split(rtrim($linha, chr(10).chr(13)."\n"."\r"), 1);
            if( $aLinha[0] == '0' ) {
                $this->processarHeader($aLinha);
            } else if( $aLinha[0] == '9' ) {
                $this->processarTrailer($aLinha);
            } else {
                $this->incrementDetalhe();
                if($this->processarDetalhe($aLinha) === false)
                {
                    unset($this->detalhe[$this->increment]);
                    $this->increment--;
                }
            }
        }
        if(method_exists($this, 'finalize')) {
            $this->finalize();
        }
        unset($this->cnab);
    }

    /**
     * Retorna o array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [
            'header' => $this->header->toArray(),
            'trailer' => $this->trailer->toArray(),
        ];
        foreach($this->detalhe as $detalhe)
        {
            $array['detalhe'][] = $detalhe->toArray();
        }
        return $array;
    }

    /**
     * Remove trecho do array.
     *
     * @param $i
     * @param $f
     * @param $array
     *
     * @return string
     * @throws \Exception
     */
    protected function rem($i, $f, $array)
    {
        $i--;

        if ($i > 398 || $f > 400) {
            throw new \Exception('$ini ou $fim ultrapassam o limite máximo de 400');
        }

        if ($f < $i) {
            throw new \Exception('$ini é maior que o $fim');
        }

        $t = $f - $i;

        return trim(implode('', array_splice($array, $i, $t)));
    }

    public function current() {
        return $this->detalhe[$this->_position];
    }

    public function next() {
        ++$this->_position;
    }

    public function key() {
        return $this->_position;
    }

    public function valid() {
        return isset($this->detalhe[$this->_position]);
    }

    public function rewind() {
        $this->_position = 0;
    }

    public function count() {
        return count($this->detalhe);
    }

    public function seek($position) {
        $this->_position = $position;
        if (!$this->valid()) {
            throw new \OutOfBoundsException('"Posição inválida "$position"');
        }
    }
}