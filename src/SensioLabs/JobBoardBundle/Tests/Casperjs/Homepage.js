var url = casper.cli.get("url");

require("utils").dump(url);

casper.test.begin('Announcement list - Infinite scroll tests', 9, function(test) {
    casper.start(url).then(function() {
        test.info('When we go to '+ url +'/ we want to see 10 announcements:');
        test.assertElementCount('.box', 10);
    });

    casper.then(function() {
        test.info('Then we scroll down and we see 10 more announcements:')
        this.scrollToBottom();
        this.wait(1000, function() {
            test.assertElementCount('.box', 20);
        });
    });

    casper.then(function() {
        test.info('The "France" link exists:');
        this.test.assertSelectorHasText('#left > div:nth-child(1) > ul > li:nth-child(2) > a', 'France (20)');
    });

    casper.thenClick('a[href*="?country=FR"]');
    casper.then(function() {
        test.info('We click on "France (20)" link, he become active:');
        this.test.assertSelectorHasText('#left > div:nth-child(1) > ul > li.active > a', 'France (20)');
        test.info('And we see 10 announcements:');
        test.assertElementCount('.box', 10);
    });

    casper.then(function() {
        test.info('The "Full Time" link exists:');
        this.test.assertSelectorHasText('#left > div:nth-child(2) > ul > li:nth-child(2) > a', 'Full Time (20)');
    });

    casper.thenClick('a[href*="?country=FR&contract-type=Full+Time"]');
    casper.then(function() {
        test.info('We click on "Full Time (20)" link, he become active:');
        this.test.assertSelectorHasText('#left > div:nth-child(2) > ul > li.active > a', 'Full Time (20)');
        test.info('And we see 10 announcements:');
        test.assertElementCount('.box', 10);
    });

    casper.then(function() {
        test.info('Then we scroll down and we see 10 more announcements:');
        this.scrollToBottom();
        this.wait(1000, function() {
            test.assertElementCount('.box', 20);
        });
    });

    casper.run(function() {
        test.done();
    });
});

casper.run();
