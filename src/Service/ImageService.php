<?php
/**
 * ImageService
 */

namespace App\Service;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageService
 */
class ImageService
{
    const PAGINATOR_ITEMS_PER_PAGE = 10;

    /** @var ImageRepository */
    private $imageRepository;
    /** @var PaginatorInterface */
    private $paginator;
    /** @var FileUploader */
    private $fileUploader;
    /** @var Filesystem */
    private $filesystem;

    /**
     * ImageService constructor.
     * @param \App\Repository\ImageRepository          $imageRepository
     * @param \Knp\Component\Pager\PaginatorInterface  $paginator
     * @param \App\Service\FileUploader                $fileUploader
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(ImageRepository $imageRepository, PaginatorInterface $paginator, FileUploader $fileUploader, Filesystem $filesystem)
    {
        $this->imageRepository = $imageRepository;
        $this->paginator = $paginator;
        $this->fileUploader = $fileUploader;
        $this->filesystem = $filesystem;
    }

    /**
     * @param array $filters
     * @param int   $page
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function createPaginatedList(array $filters, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->imageRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Zapisuje do encję do bazy i aktualizuje plik, jeśli przesłano
     * @param \App\Entity\Image                                        $image
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|null $file
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Image $image, UploadedFile $file = null)
    {
        // ścieżka do usunięcia poprzedniego pliku
        $previousFilename = '';

        if (null !== $file) {
            // upload na server
            $uploadedFile = $this->fileUploader->upload($file);
            // pobranie poprzeczniej ścieżki
            $previousFilename = $image->getFilename();
            // ustawienie ścieżki zuploadowanego pliku w encji
            $image->setFilename($uploadedFile);
        }

        // zapis do bazy
        $this->imageRepository->save($image);

        // usuwanie poprzedniego zdjecia, jesli istnieje
        $this->removePreviousFile($previousFilename);
    }

    /**
     * Usuwa z bazy
     *
     * @param \App\Entity\Image $image
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Image $image)
    {
        // pobranie poprzeczniej ścieżki
        $previousFilename = $image->getFilename();
        // usunięcie z bazy
        $this->imageRepository->delete($image);
        // usuwa plik z dysku
        $this->removePreviousFile($previousFilename);
    }

    /**
     * Ustala ścieżkę i usuwa plik z dysku
     *
     * @param string $previousFilename
     */
    protected function removePreviousFile(string $previousFilename)
    {
        if (!empty($previousFilename)) {
            $previousFilepath = $this->fileUploader->getTargetDirectory().DIRECTORY_SEPARATOR.$previousFilename;
            if (file_exists($previousFilepath)) {
                $this->filesystem->remove($previousFilepath);
            }
        }
    }
}
