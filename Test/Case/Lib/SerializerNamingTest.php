<?php

App::uses('SerializerNaming', 'Serializers.Lib');

class SerializerNamingTest extends CakeTestCase {

	public function setUp() {
		$this->naming = new SerializerNaming;
	}

	public function testClassifyConvertsPluralSingleLowercaseWord() {
		$this->assertEquals('PostSerializer', $this->naming->classify('posts'));
	}

	public function testClassifyConvertsSingularSingleLowercaseWord() {
		$this->assertEquals('ArticleSerializer', $this->naming->classify('article'));
	}

	public function testClassifyConvertsSingularMultiLowercaseWords() {
		$this->assertEquals('BlogPostSerializer', $this->naming->classify('blog_post'));
	}

	public function testClassifyConvertsPluralMultiLowercaseWords() {
		$this->assertEquals('PostTagSerializer', $this->naming->classify('post_tags'));
	}

	public function testClassifyConvertsPluralSingleCapitalizeWord() {
		$this->assertEquals('PostSerializer', $this->naming->classify('Posts'));
	}

	public function testClassifyConvertsSingularSingleCapitalizeWord() {
		$this->assertEquals('ArticleSerializer', $this->naming->classify('Article'));
	}

	public function testClassifyConvertsSingularMultiCapitalizeWords() {
		$this->assertEquals('BlogPostSerializer', $this->naming->classify('BlogPost'));
	}

	public function testClassifyConvertsPluralMultiCapitalizedWords() {
		$this->assertEquals('PostTagSerializer', $this->naming->classify('PostTags'));
	}

	public function testClassifyConvertsAnythingThatEndsWithSerializer() {
		$val = $this->naming->classify('SessionSerializer');
		$this->assertEquals('SessionSerializer', $val);
	}

	public function testClassifyConvertsAnythingThatBeginsWithSerializer() {
		$val = $this->naming->classify('SerializerUser');
		$this->assertEquals('SerializerUserSerializer', $val);
	}
}
