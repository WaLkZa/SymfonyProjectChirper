<?php

namespace ChirperBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="ChirperBundle\Repository\UserRepository")
 */
class User implements UserInterface
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
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChirperBundle\Entity\Chirp", mappedBy="author")
     */
    private $chirps;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="ChirperBundle\Entity\Role")
     * @ORM\JoinTable(name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")})
     */
    private $roles;

    /**
     * Many Users have Many Users.
     * @ORM\ManyToMany(targetEntity="User", mappedBy="following")
     */
    private $followers;

    /**
     * Many Users have many Users.
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
     * @ORM\JoinTable(name="followers",
     *      joinColumns={@ORM\JoinColumn(name="follower_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="followed_id", referencedColumnName="id")}
     *      )
     */
    private $following;

    /**
     * @var int
     *
     * @ORM\Column(name="followersCounter", type="integer")
     */
    private $followersCounter;

    /**
     * @var int
     *
     * @ORM\Column(name="followingCounter", type="integer")
     */
    private $followingCounter;

    /**
     * @var int
     *
     * @ORM\Column(name="chirpsCounter", type="integer")
     */
    private $chirpsCounter;

    /**
     * Many Users can like Many Chirps.
     * @ORM\ManyToMany(targetEntity="ChirperBundle\Entity\Chirp")
     * @ORM\JoinTable(name="likes",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="chirp_id", referencedColumnName="id")}
     *      )
     */
    private $chirpLikes;

    public function __construct()
    {
        $this->chirps = new ArrayCollection();
        $this->roles = new ArrayCollection();

        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();

        $this->chirpLikes = new ArrayCollection();
        $this->followersCounter = 0;
        $this->followingCounter = 0;
        $this->chirpsCounter = 0;
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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return array('ROLE_USER');
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $stringRoles = [];
        foreach ($this->roles as $role)
        {
            /**
             * @var $role Role
             */
            $stringRoles[] = $role->getRole();
        }

        return $stringRoles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return ArrayCollection
     */
    public function getChirps()
    {
        return $this->chirps;
    }

    /**
     * @param Chirp chirp
     * @return User
     */
    public function addPost(Chirp $chirp)
    {
        $this->chirps[] = $chirp;
        return $this;
    }

    /**
     * @param Role $role
     *
     * @return User
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;
        return $this;
    }

    /**
     * @param Chirp $chirp
     * @return bool
     */
    public function isAuthor(Chirp $chirp) {
        return $chirp->getAuthorId() === $this->getId();
    }

    /**
     * @return bool
     */
    public function isAdmin() {
        return in_array("ROLE_ADMIN", $this->getRoles());
    }

    /**
     * @return ArrayCollection
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * @param User $follower
     * @return User
     */
    public function setFollower(User $follower)
    {
        $this->followers[] = $follower;
        return $this;
    }

    public function removeFollower(User $follower)
    {
        $this->followers->removeElement($follower);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * @param User $following
     * @return User
     */
    public function setFollowing(User $following)
    {
        $this->following[] = $following;
        return $this;
    }

    public function removeFollowing(User $following)
    {
        $this->following->removeElement($following);
        return $this;
    }

    public function isUserFollowed(User $following)
    {
        return $this->following->contains($following);
    }

    /**
     * @return ArrayCollection
     */
    public function getChirpLikes()
    {
        return $this->chirpLikes;
    }

    public function setChirpLike(Chirp $chirpLike)
    {
        $this->chirpLikes[] = $chirpLike;
        return $this;
    }

    public function removeChirpLike(Chirp $chirpLike)
    {
        $this->chirpLikes->removeElement($chirpLike);
        return $this;
    }

    public function isExistChirpLike(Chirp $chirpLike)
    {
        return $this->chirpLikes->contains($chirpLike);
    }

    /**
     * @return int
     */
    public function getFollowersCounter()
    {
        return $this->followersCounter;
    }

    public function incrementFollowersCounter()
    {
        $this->followersCounter++;
    }

    public function decrementFollowersCounter()
    {
        $this->followersCounter--;
    }

    /**
     * @return int
     */
    public function getFollowingCounter()
    {
        return $this->followingCounter;
    }

    public function incrementFollowingCounter()
    {
        $this->followingCounter++;
    }

    public function decrementFollowingCounter()
    {
        $this->followingCounter--;
    }

    /**
     * @return int
     */
    public function getChirpsCounter()
    {
        return $this->chirpsCounter;
    }

    public function incrementChirpsCounter()
    {
        $this->chirpsCounter++;
    }

    public function decrementChirpsCounter()
    {
        $this->chirpsCounter--;
    }
}

