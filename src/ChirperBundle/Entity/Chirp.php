<?php

namespace ChirperBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Chirp
 *
 * @ORM\Table(name="chirps")
 * @ORM\Entity(repositoryClass="ChirperBundle\Repository\ChirpRepository")
 */
class Chirp
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
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var int
     * @ORM\Column(name="authorId", type="integer")
     */
    private $authorId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="ChirperBundle\Entity\User", inversedBy="chirps")
     * @ORM\JoinColumn(name="authorId", referencedColumnName="id")
     */
    private $author;

    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Chirp
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     *
     * @return Chirp
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return string
     */
    public function getDateAdded()
    {
        return $this->calculateTime($this->dateAdded);
   }

   /**
    * @return string
    */
    public function getSummary()
    {
        if (strlen($this->content) > 50)
        {
            $this->setSummary();
        }
        return $this->summary;
    }

    public function setSummary()
    {
        $this->summary = substr($this->getContent(),
            0,
            strlen($this->getContent() / 2) . "...");
    }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param integer $authorId
     *
     * @return Chirp
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
        return $this;
    }
    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return Chirp
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;
        return $this;
    }

    private function calculateTime($date)
    {
        $currentDate = new DateTime('now');
        $sinceStartDate = $currentDate->diff($date);

        $minutes = $sinceStartDate->days * 24 * 60;
        $minutes += $sinceStartDate->h * 60;
        $minutes += $sinceStartDate->i;

        if ($minutes < 1) {
            return 'less than a minute';
        }

        if ($minutes < 60) {
            return $minutes . ' minute' . $this->pluralize($minutes);
        }

        $minutes = floor($minutes / 60);

        if ($minutes < 24) {
            return $minutes . ' hour' . $this->pluralize($minutes);
        }

        $minutes = floor($minutes / 24);

        if ($minutes < 30) {
            return $minutes . ' day' . $this->pluralize($minutes);
        }

        $minutes = floor($minutes / 30);

        if ($minutes < 12) {
            return $minutes . ' month' . $this->pluralize($minutes);
        }

        $minutes = floor($minutes / 12);

        return $minutes . ' year' . $this->pluralize($minutes);
    }

    private function pluralize($value)
    {
        return $value !== 1 ? 's' : '';
    }
}

