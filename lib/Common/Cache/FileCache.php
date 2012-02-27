<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Common\Cache;

/**
 * Array cache driver.
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   2.x
 * @author  Taylor Otwell <taylorotwell@gmail.com>
 */
class FileCache extends CacheProvider
{
    /**
     * @var array $data
     */
    private $data = array();

    /**
     * Create a new FileCache CacheProvider.
     *
     * @param  string  $cachePath  the directory where the cache files should be stored
     */
    public function __construct($cachePath)
    {
        $this->cachePath = $cachePath;

        $this->warmCache();
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return (isset($this->data[$id])) ? $this->data[$id] : false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return isset($this->data[$id]);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $this->data[$id] = $data;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        unset($this->data[$id]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        @unlink($this->cachePath);

        $this->data = array();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        return null;
    }

    /**
     * Load the file cache from disk.
     */
    protected function warmCache()
    {
        if (file_exists($this->cachePath))
        {
            $this->data = unserialize(file_get_contents($this->cachePath));
        }
    }

    /**
     * Handle the destruction of the provider.
     *
     * Write the cache out to disk.
     */
    public function __destruct()
    {
        file_put_contents($this->cachePath, serialize($this->data));
    }

}