<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Middleware\Cookie;

use Dflydev\FigCookies\SetCookie;
use RuntimeException;

final class FileStorage implements Storage
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var SetCookie[]
     */
    private $storage;

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->storage = [];

        if (file_exists($this->filePath)) {
            $this->load();
        }
    }

    public function __destruct()
    {
        $this->save();
    }

    /**
     * @inheritdoc
     */
    public function add(SetCookie $setCookie)
    {
        $key = sprintf('%s__%s__%s', $setCookie->getDomain(), $setCookie->getPath(), $setCookie->getName());
        $this->storage[$key] = $setCookie;
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return array_values($this->storage);
    }

    private function save()
    {
        $data = [];
        foreach ($this->storage as $setCookie) {
            $data[] = $setCookie->__toString();
        }

        $json = json_encode($data);
        if (false === file_put_contents($this->filePath, $json)) {
            throw new RuntimeException(sprintf('Unable to save file %s.', $this->filePath));
        }
    }

    private function load()
    {
        $json = file_get_contents($this->filePath);
        if (false === $json) {
            throw new RuntimeException(sprintf('Unable to load file %s.', $this->filePath));
        } else if ($json === '') {
            return;
        }

        $data = json_decode($json);
        foreach ($data as $setCookieString) {
            $this->add(SetCookie::fromSetCookieString($setCookieString));
        }
    }
}
