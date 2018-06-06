<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/6/2018
 * Time: 1:09 PM
 */

namespace App\Manager;


use App\Entity\Folder;
use Doctrine\Common\Collections\ArrayCollection;
use PhpParser\Node\Expr\Array_;

class FolderManager
{
    private $folders;
    
    public function __construct()
    {
        $this->folders = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getFolders(): ArrayCollection
    {
        return $this->folders;
    }
    
    public function getFolderByName($name) {
        foreach ($this->folders as $folder ) {
            if($folder->getName() == $name)
            {
                return $folder;
            }
        }
        
        return null;
    }

    public function addFolder(Folder $folder): self
    {
        if (!$this->folders->contains($folder)) {
            $this->folders[] = $folder;
        }

        return $this;
    }

    /**
     * @param ArrayCollection $folders
     */
    public function setFolders(ArrayCollection $folders): void
    {
        $this->folders = $folders;
    }
    
    

}