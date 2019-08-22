<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuickLink
 *
 * @ORM\Table(name="quick_link")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuickLinkRepository")
 */
class QuickLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */

    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="icon_url", type="string", length=255, nullable=true)
     */
    private $iconUrl;

    /**
     * @var int
     *
     * @ORM\Column(name="order_num", type="integer")
     */
    private $orderNum;

    /**
     * @var bool
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="quickLinks")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $owner;

    /**
     * Get id.
     *
     * @return int
     */

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return QuickLink
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return QuickLink
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set iconUrl.
     *
     * @param string $iconUrl
     *
     * @return QuickLink
     */
    public function setIconUrl($iconUrl)
    {
        $this->iconUrl = $iconUrl;

        return $this;
    }

    /**
     * Get iconUrl.
     *
     * @return string
     */
    public function getIconUrl()
    {
        return $this->iconUrl;
    }

    /**
     * Set orderNum.
     *
     * @param int $orderNum
     *
     * @return QuickLink
     */
    public function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    /**
     * Get orderNum.
     *
     * @return int
     */
    public function getOrderNum()
    {
        return $this->orderNum;
    }

    /**
     * Set visible.
     *
     * @param bool $visible
     *
     * @return QuickLink
     */
    public function setVisible(bool $visible): QuickLink
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible.
     *
     * @return bool
     */
    public function getVisible(): bool
    {
        return $this->visible;
    }

    /**
     * @return User | null
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @param User|null $owner
     * @return QuickLink
     */
    public function setOwner(?User $owner): QuickLink
    {
        $this->owner = $owner;

        return $this;
    }

    public static function getDefaults(){
        $quickLink1 = new QuickLink();
        $quickLink2 = new QuickLink();
        $quickLink3 = new QuickLink();
        $quickLink4 = new QuickLink();
        $quickLink1->setUrl("www.google.no")
            ->setVisible(true)
            ->setTitle('Google')
            ->setOrderNum(1);
        $quickLink2->setUrl("test")
            ->setTitle("tester2")
            ->setVisible(true)
            ->setOrderNum(2);
        $quickLink3->setUrl("test3")
            ->setTitle("test3")
            ->setVisible(true)
            ->setOrderNum(3);
        $quickLink4->setUrl("test 4")
            ->setTitle("test 4")
            ->setVisible(true)
            ->setOrderNum(4);
        return [$quickLink1,$quickLink2,$quickLink3,$quickLink4];
    }
}
