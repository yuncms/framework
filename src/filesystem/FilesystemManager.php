<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\filesystem;

use Closure;
use OSS\OssClient;
use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yuncms\helpers\ArrayHelper;
use Aws\S3\S3Client;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Adapter\Ftp as FtpAdapter;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\AwsS3v3\AwsS3Adapter as S3Adapter;
use League\Flysystem\Cached\Storage\Memory as MemoryStore;
use yuncms\filesystem\adapters\OssAdapter;

/**
 * Class Storage
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 *
 * @property string $visibility
 * @property string $defaultAdapter
 * @property string $defaultCloudAdapter
 * @property array $disks
 */
class FilesystemManager extends Component
{
    /**
     * Default Filesystem Disk
     *
     * @var string
     */
    public $default = 'local';

    /**
     * Default Cloud Filesystem Disk
     *
     * @var string
     */
    public $cloud = 'oss';

    /**
     * 磁盘配置
     * @var array
     */
    public $disks = [];

    /**
     * @var array
     */
    private $_disks = [];

    /**
     * 获取磁盘
     * @param string|null $disk
     * @return \yuncms\filesystem\Filesystem|\yuncms\filesystem\Cloud
     * @throws \yii\base\InvalidConfigException
     */
    public function cloud($disk = null)
    {
        $disk = !is_null($disk) ? $disk : $this->cloud;
        return $this->get($disk);
    }

    /**
     * 获取磁盘
     * @param string|null $disk
     * @return \yuncms\filesystem\Filesystem|\yuncms\filesystem\Cloud
     * @throws \yii\base\InvalidConfigException
     */
    public function disk($disk = null)
    {
        $disk = !is_null($disk) ? $disk : $this->default;
        return $this->get($disk);
    }

    /**
     * Attempt to get the disk from the local cache.
     *
     * @param  string $name
     * @return \yuncms\filesystem\Filesystem|\yuncms\filesystem\Cloud
     * @throws \yii\base\InvalidConfigException
     */
    protected function get($name)
    {
        return $this->_disks[$name] ?? $this->resolve($name);
    }

    /**
     * Set the given disk instance.
     *
     * @param  string $name
     * @param  mixed $disk
     * @return void
     */
    public function set($name, $disk)
    {
        $this->disks[$name] = $disk;
    }

    /**
     * Resolve the given disk.
     *
     * @param  string $name
     * @return object|Filesystem
     * @throws \yii\base\InvalidConfigException
     */
    protected function resolve($name)
    {
        $config = $this->disks[$name];
        if (isset($config['class'])) {
            return Yii::createObject($config);
        } else if (isset($config['adapter'])) {
            $driverMethod = 'create' . ucfirst($config['adapter']) . 'Adapter';
            if (method_exists($this, $driverMethod)) {
                return $this->{$driverMethod}($config);
            } else {
                throw new InvalidArgumentException("Adapter [{$config['adapter']}] is not supported.");
            }
        } else {
            throw new InvalidArgumentException("Wrong adapter configuration.");
        }
    }

    /**
     * Create an instance of the local adapter.
     *
     * @param  array $config
     * @return \yuncms\filesystem\Filesystem
     */
    public function createLocalAdapter(array $config)
    {
        $permissions = $config['permissions'] ?? [];
        $root = Yii::getAlias($config['root']);

        $links = ($config['links'] ?? null) === 'skip'
            ? LocalAdapter::SKIP_LINKS
            : LocalAdapter::DISALLOW_LINKS;

        return $this->adapt($this->createFlysystem(new LocalAdapter(
            $root, LOCK_EX, $links, $permissions
        ), $config));
    }

    /**
     * Create an instance of the oss driver.
     *
     * @param  array $config
     * @return \yuncms\filesystem\Filesystem
     * @throws \OSS\Core\OssException
     * @throws \Exception
     */
    public function createOssAdapter(array $config)
    {
        $root = $config['root'] ?? null;
        $oss = new OssClient($config['access_id'], $config['access_secret'], $config['endpoint'],
            $config['isCName'] ?? false,
            $config['securityToken'] ?? null,
            $config['proxy'] ?? null
        );
        $oss->setTimeout($config['timeout'] ?? 3600);
        $oss->setConnectTimeout($config['connectTimeout'] ?? 10);
        $oss->setUseSSL($config['useSSL'] ?? false);
        return $this->adapt($this->createFlysystem(
            new OssAdapter($oss, $config['bucket'], $root), $config
        ));
    }

    /**
     * Create an instance of the ftp driver.
     *
     * @param  array $config
     * @return \yuncms\filesystem\Filesystem
     */
    public function createFtpAdapter(array $config)
    {
        return $this->adapt($this->createFlysystem(
            new FtpAdapter($config), $config
        ));
    }

    /**
     * Create an instance of the Amazon S3 driver.
     *
     * @param  array $config
     * @return Filesystem
     */
    public function createS3Adapter(array $config)
    {
        $s3Config = $this->formatS3Config($config);
        $root = $s3Config['root'] ?? null;
        $options = $config['options'] ?? [];
        return $this->adapt($this->createFlysystem(
            new S3Adapter(new S3Client($s3Config), $s3Config['bucket'], $root, $options), $config
        ));
    }

    /**
     * Format the given S3 configuration with the default options.
     *
     * @param  array $config
     * @return array
     */
    protected function formatS3Config(array $config)
    {
        $config += ['version' => 'latest'];
        if ($config['key'] && $config['secret']) {
            $config['credentials'] = ArrayHelper::only($config, ['key', 'secret']);
        }
        return $config;
    }

    /**
     * Create a Flysystem instance with the given adapter.
     *
     * @param  \League\Flysystem\AdapterInterface $adapter
     * @param  array $config
     * @return \League\Flysystem\FilesystemInterface
     */
    protected function createFlysystem(AdapterInterface $adapter, array $config)
    {
        $cache = ArrayHelper::pull($config, 'cache');
        $config = ArrayHelper::only($config, ['visibility', 'disable_asserts', 'url']);
        if ($cache) {
            $adapter = new CachedAdapter($adapter, $this->createCacheStore($cache));
        }
        return new Flysystem($adapter, count($config) > 0 ? $config : null);
    }

    /**
     * Create a cache store instance.
     *
     * @param  mixed $config
     * @return \League\Flysystem\Cached\CacheInterface
     * @throws \InvalidArgumentException
     */
    protected function createCacheStore($config)
    {
        if ($config === true) {
            return new MemoryStore;
        }
        return new Cache(Yii::$app->cache,
            $config['prefix'] ?? 'flysystem',
            $config['duration'] ?? null
        );
    }

    /**
     * Adapt the filesystem implementation.
     *
     * @param  \League\Flysystem\FilesystemInterface $filesystem
     * @return \yuncms\filesystem\Filesystem
     */
    protected function adapt(FilesystemInterface $filesystem)
    {
        return new FilesystemAdapter($filesystem);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultAdapter()
    {
        return $this->default;
    }

    /**
     * Get the default cloud driver name.
     *
     * @return string
     */
    public function getDefaultCloudAdapter()
    {
        return $this->cloud;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function __call($method, $parameters)
    {
        return $this->disk()->$method(...$parameters);
    }
}
