<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package     spoon
 * @subpackage  feed
 * @author      Davy Hellemans <davy@spoon-library.com>
 * @since       1.1.0
 */

/**
 * This base class provides all the methods used by Atom RSS-items.
 *
 * @package     spoon
 * @subpackage  feed
 * @author      Lowie Benoot <lowiebenoot@netlash.com>
 * @since       1.1.0
 */
class SpoonFeedAtomRSSItem
{
    private array $authors = [];
    private array $categories = [];
    private ?string $content = null;
    private array $contributors = [];
    private string $id;
    private array $links = [];
    private ?int $publicationDate = null;
    private ?string $rights = null;
    private string $summary;
    private string $title;
    private ?int $updatedDate = null;

    public function __construct(string $title, string $id, string $summary)
    {
        $this->setTitle($title);
        $this->setId($id);
        $this->setSummary($summary);
    }

    public function addAuthor(array $author): void
    {
        $this->authors[] = $author;
    }

    public function addCategory(array $category): void
    {
        $this->categories[] = $category;
    }

    public function addContributor(string $name, ?string $email = null, ?string $uri = null): void
    {
        $this->contributors[] = array_filter([
            'name' => $name,
            'email' => $email,
            'uri' => $uri
        ]);
    }

    public function addLink(array $link): void
    {
        $this->links[] = $link;
    }

    public function buildXML(): string
    {
        $xml = "<entry>\n";
        $xml .= "\t<title>{$this->title}</title>\n";
        $xml .= "\t<id>{$this->id}</id>\n";
        
        foreach ($this->authors as $author) {
            $xml .= "\t<author>\n\t\t<name>{$author['name']}</name>\n";
            if (isset($author['email'])) $xml .= "\t\t<email>{$author['email']}</email>\n";
            if (isset($author['uri'])) $xml .= "\t\t<uri>{$author['uri']}</uri>\n";
            $xml .= "\t</author>\n";
        }

        if ($this->updatedDate) {
            $xml .= "\t<updated>" . date('Y-m-d\TH:i:s\Z', $this->updatedDate) . "</updated>\n";
        }

        if ($this->publicationDate) {
            $xml .= "\t<published>" . date('Y-m-d\TH:i:s\Z', $this->publicationDate) . "</published>\n";
        }

        if ($this->rights) {
            $xml .= "\t<rights>{$this->rights}</rights>\n";
        }

        foreach ($this->links as $link) {
            $xml .= "\t<link ";
            foreach ($link as $key => $value) {
                $xml .= "{$key}='" . htmlentities($value) . "' ";
            }
            $xml .= "/>\n";
        }

        foreach ($this->categories as $category) {
            $xml .= "\t<category ";
            foreach ($category as $key => $value) {
                $xml .= "{$key}='{$value}' ";
            }
            $xml .= "/>\n";
        }

        $xml .= "\t<summary type='html'><![CDATA[{$this->summary}]]></summary>\n";
        if ($this->content) {
            $xml .= "\t<content type='html'><![CDATA[{$this->content}]]></content>\n";
        }
        $xml .= "</entry>\n";

        return $xml;
    }

    public function getAuthors(): array { return $this->authors; }
    public function getCategories(): array { return $this->categories; }
    public function getContent(): ?string { return $this->content; }
    public function getContributors(): array { return $this->contributors; }
    public function getId(): string { return $this->id; }
    public function getLinks(): array { return $this->links; }
    public function getPublicationDate(): ?int { return $this->publicationDate; }
    public function getRights(): ?string { return $this->rights; }
    public function getSummary(): string { return $this->summary; }
    public function getTitle(): string { return $this->title; }
    public function getUpdatedDate(): ?int { return $this->updatedDate; }

    public static function isValid(SimpleXMLElement $item): bool
    {
        return isset($item->title, $item->id, $item->summary);
    }

    public function parse(): string
    {
        return $this->buildXML();
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setId(string $id): void
    {
        if (!filter_var($id, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid URL: {$id}");
        }
        $this->id = $id;
    }

    public function setPublicationDate(int $publicationDate): void
    {
        $this->publicationDate = $publicationDate;
    }

    public function setRights(string $rights): void
    {
        $this->rights = $rights;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setUpdatedDate(int $updatedDate): void
    {
        $this->updatedDate = $updatedDate;
    }
}
