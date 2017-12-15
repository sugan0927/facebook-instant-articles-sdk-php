<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

use Facebook\InstantArticles\Validators\Type;

/**
 * Class Slideshow
 * This element Class is the slideshow for the article.
 *
 * Example:
 * <figure class="op-slideshow">
 *     <figure>
 *         <img src="http://mydomain.com/path/to/img1.jpg" />
 *     </figure>
 *     <figure>
 *         <img src="http://mydomain.com/path/to/img2.jpg" />
 *     </figure>
 *     <figure>
 *         <img src="http://mydomain.com/path/to/img3.jpg" />
 *     </figure>
 * </figure>
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/image}
 */
class Slideshow extends Audible implements ChildrenContainer, Captionable
{
    /**
     * @var Caption The caption for the Slideshow
     */
    private ?Caption $caption;

    /**
     * @var Image[] the images hosted on web that will be shown on the slideshow
     */
    private vec<Image> $article_images = vec[];

    /**
     * @var GeoTag The json geotag content inside the script geotag
     */
    private ?GeoTag $geotag;

    /**
     * @var Audio The audio if the Slideshow uses audio
     */
    private ?Audio $audio;

    /**
     * @var Cite The attribution citation text in the <cite>...</cite> tags.
     */
    private ?Cite $attribution;

    private function __construct()
    {
    }

    /**
     * Factory method for the Slideshow
     *
     * @return Slideshow the new instance
     */
    public static function create(): Slideshow
    {
        return new self();
    }

    /**
     * This sets figcaption tag as documentation. It overrides all sets
     * made with Caption.
     *
     * @see Caption.
     * @param Caption $caption the caption the slideshow will have
     *
     * @return $this
     */
    public function withCaption(Caption $caption): this
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Sets the Image list of images for the slideshow. It is REQUIRED.
     *
     * @param vec<Image> The images. Ie: http://domain.com/img.png
     *
     * @return $this
     */
    public function withImages(vec<Image> $article_images): this
    {
        $this->article_images = $article_images;

        return $this;
    }

    /**
     * Adds a new image to the slideshow. It is REQUIRED.
     *
     * @param Image $article_image The image.
     *
     * @return $this
     */
    public function addImage(Image $article_image): this
    {
        $this->article_images[] = $article_image;

        return $this;
    }

    /**
     * Sets the geotag on the slideshow.
     *
     * @see {link:http://geojson.org/}
     *
     * @param GeoTag $json
     *
     * @return $this
     */
    public function withMapGeoTag(GeoTag $json): this
    {
        $this->geotag = $json; // TODO Validate the json informed

        return $this;
    }

    /**
     * Adds audio to this slideshow.
     *
     * @param Audio $audio The audio object
     *
     * @return $this
     */
    public function withAudio(Audio $audio): this
    {
        $this->audio = $audio;

        return $this;
    }

    /**
     * @return Caption The caption object
     */
    public function getCaption(): ?Caption
    {
        return $this->caption;
    }

    /**
     * @return vec<Image> The ArticleImages content of the slideshow
     */
    public function getArticleImages(): vec<Image>
    {
        return $this->article_images;
    }

    /**
     * @return string the json for geotag unescaped content
     */
    public function getGeotag(): ?GeoTag
    {
        return $this->geotag;
    }

    /**
     * @return Audio The audio object
     */
    public function getAudio(): ?Audio
    {
        return $this->audio;
    }

    /**
     * @return string the <cite> content
     */
    public function getAttribution(): ?Cite
    {
        return $this->attribution;
    }

    /**
     * Structure and create the full Slideshow in a XML format DOMNode.
     *
     * @param \DOMDocument $document where this element will be appended. Optional
     *
     * @return \DOMNode
     */
    public function toDOMElement(\DOMDocument $document): \DOMNode
    {
        if (!$this->isValid()) {
            return $this->emptyElement($document);
        }

        $element = $document->createElement('figure');
        $element->setAttribute('class', 'op-slideshow');

        // URL markup required
        if ($this->article_images) {
            foreach ($this->article_images as $article_image) {
                $article_image_element = $article_image->toDOMElement($document);
                $element->appendChild($article_image_element);
            }
        }

        // Caption markup optional
        if ($this->caption) {
            $element->appendChild($this->caption->toDOMElement($document));
        }

        // Geotag markup optional
        if ($this->geotag) {
            // $script_element = $document->createElement('script');
            // $script_element->setAttribute('type', 'application/json');
            // $script_element->setAttribute('class', 'op-geotag');
            // $script_element->appendChild($document->createTextNode($this->geotag));
            // $element->appendChild($script_element);
            $element->appendChild($this->geotag->toDOMElement($document));
        }

        // Audio markup optional
        if ($this->audio) {
            $element->appendChild($this->audio->toDOMElement($document));
        }

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid Slideshow that contains at least one Image valid, false otherwise.
     */
    public function isValid(): bool
    {
        foreach ($this->article_images as $item) {
            if ($item->isValid()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Implements the ChildrenContainer::getContainerChildren().
     *
     * @see ChildrenContainer::getContainerChildren().
     * @return vec of Elements contained by Image.
     */
    public function getContainerChildren(): vec<Element>
    {
        $children = vec[];

        if ($this->article_images) {
            foreach ($this->article_images as $article_image) {
                $children[] = $article_image;
            }
        }

        if ($this->caption) {
            $children[] = $this->caption;
        }

        // Audio markup optional
        if ($this->audio) {
            $children[] = $this->audio;
        }

        return $children;
    }
}
