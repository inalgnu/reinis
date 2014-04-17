var url = casper.cli.get("url");

require("utils").dump(url);

casper.test.begin('Announcement list - Infinite scroll tests', 9, function(test) {
    casper.start(url).then(function() {
        this.echo('When we go to '+ url +'/ we want to see 10 announcements:');
        test.assertElementCount('.box', 10);
    });

    casper.then(function() {
        this.echo('Then we scroll down and we see 10 more announcements:')
        this.scrollTo(0, 2000);
        this.wait(1000, function() {
            test.assertElementCount('.box', 20);
        });
    });

    casper.then(function() {
        this.echo('The "France" link exists:');
        this.test.assertSelectorHasText('#left > div:nth-child(1) > ul > li:nth-child(2) > a', 'France (20)');
    });

    casper.thenClick('#left > div:nth-child(1) > ul > li:nth-child(2) > a');

    casper.then(function() {
        this.echo('We click on "France (20)", he become active:');
        this.test.assertSelectorHasText('#left > div:nth-child(1) > ul > li.active > a', 'France (20)');
        this.echo('And we see 10 announcements:');
        test.assertElementCount('.box', 10);
    });

    casper.then(function() {
        this.echo('The "Full Time" link exists:');
        this.test.assertSelectorHasText('#left > div:nth-child(2) > ul > li:nth-child(2) > a', 'Full Time (30)');
    });

    casper.thenClick('#left > div:nth-child(2) > ul > li:nth-child(2) > a');

    casper.then(function() {
        this.echo('We click on the "Full Time (30)", he become active:');
        this.test.assertSelectorHasText('#left > div:nth-child(2) > ul > li.active > a', 'Full Time (30)');
        this.echo('And we see 10 announcements:');
        test.assertElementCount('.box', 10);
    });

    casper.then(function() {
        this.echo('Then we scroll down and we see 10 more announcements:');
        this.scrollTo(0, 2000);
        this.wait(1000, function() {
            test.assertElementCount('.box', 20);
        });
    });

    casper.run(function() {
        test.done();
    });
});

casper.run();
