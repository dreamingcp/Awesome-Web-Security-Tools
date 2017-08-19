#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys

def main():
    if len(sys.argv) != 2:
        print "Usage : "
        print "        python %s [SHELL_SCRIPT]" % (sys.argv[0])
        exit(1)
    filename = sys.argv[1]
    target = "/tmp/ssh_log_001.sh"
    print "[+] Loding file..."
    print "-" * 32
    print "rm -rf %s" % (target)
    print "echo > %s" % (target)
    with open(filename) as f:
        for line in f:
            # data = line.replace("'", "\\'")
            data = line[0:-1]
            print "echo '%s' >> %s" % (data, target)
    print "chmod +x %s" % (target)
    print "bash -x %s" % (target)

if __name__ == "__main__":
    main()


